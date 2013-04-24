<?php

namespace Edm\Service;

use Edm\Service\AbstractService,
//    Edm\Service\TermTaxonomyServiceAware,
//    Edm\Service\TermTaxonomyServiceAwareTrait,
    Edm\Model\User,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature,
    Zend\Stdlib\DateTime;

/**
 * @author ElyDeLaCruz
 */
class UserService extends AbstractService {

    protected $userTable;
    protected $contactTable;
    protected $userContactRelTable;
    protected $termTaxService;
    protected $resultSet;
    protected $screenNameLength = 12;

    const SALT = 'saltcontentgoeshere';
    const PEPPER = 'peppercontentgoeshere';
    const TOKEN_SEED = 'defaulttokenseed';

    public function __construct() {
        $this->sql = new Sql($this->getDb());
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype(new User());
    }

    /**
     * Creates a user and it's constituants 
     *  (contact and user contact relationship)
     * @param array $data
     * @return mixed int | boolean
     * @throws Exception
     */
    public function createUser(array $data) {
        // If no user key
        if (!array_key_exists('user', $data)) {
            throw new Exception(__CLASS__ . '.' . __FUNCTION__ .
            ' requires the data param to contain a user key.');
        }

        // If no contact key
        if (!array_key_exists('contact', $data)) {
            throw new Exception(__CLASS__ . '.' . __FUNCTION__ .
            ' requires the data param to contain a contact key.');
        }

        // @todo use only arrays to eliminate multi casting variables multiple
        // times within a function
        $user = (object) $data['user'];
        $contact = (object) $data['contact'];
        $key = isset($user->activatioKey) ? $user->activationKey : '';
        $userKeyValid = $this->is_activationKeyValid($key, $contact);

        // If no screen name generate one
        if (empty($user->screenName)) {
            $user->screenName = $this->gen_uuid($this->screenNameLength);
        }
        // If no api key and activation key is required generate
        if (!$userKeyValid) {
            $user->activationKey = $this->generateActivationKey(
                    $contact->firstName, $contact->lastName, $contact->email);
        }

        // Set status to "pending-activation"
        if (empty($user->status) || !$userKeyValid) {
            $user->status = 'pending-activation';
        }

        // If no user.role set user.role to "user"
        if (empty($user->role)) {
            $user->role = 'user';
        }

        // If no contact.type set contact.type to "user"
        if (empty($contact->type)) {
            $contact->type = 'user';
        }

        // If no access group
        if (empty($user->accessGroup)) {
            $user->accessGroup = 'cms-manager';
        }

        // Contact params default value
        if (empty($contact->userParams)) {
            $contact->userParams = '';
        }

        // Contact description default value
        if (empty($contact->description)) {
            $contact->description = '';
        }

        // If user has a password
        if (!empty($user->password)) {
            $user->password = $this->encodePassword($user->password);
        }

        // Set registeredDate
        $today = new DateTime();
        $user->registeredDate = $today->getTimestamp();

        // Escape tuples 
        $dbDataHelper = $this->getDbDataHelper();
        $cleanUser = $dbDataHelper->escapeTuple((array) $user);
        $cleanContact = $dbDataHelper->escapeTuple((array) $contact);
        $userContactRel = array(
            'email' => $cleanContact['email'],
            'screenName' => $cleanUser['screenName']);

        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();

        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create contact
            $this->getContactTable()->insert($cleanContact);
            $cleanUser['contact_id'] = $driver->getLastGeneratedValue();
            
            // Create user
            $this->getUserTable()->insert($cleanUser);
            $retVal  = $driver->getLastGeneratedValue();

            // Create user contact rel
            $this->getUserContactRelTable()->insert($userContactRel);
            
            // Commit and return true
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            $retVal = false;
        }
        return $retVal;
    }

    /**
     * Updates a user and it's constituants
     *   (contact and user contact relationship).  
     * @todo There are no safety checks being done in this method
     * @param int $id
     * @param array $data
     * @param int $contactId
     * @return boolean
     * @throws Exception
     */
    public function updateUser($id, array $data, $contactId = null) {
        // If no user key
        if (!array_key_exists('user', $data)) {
            throw new Exception(__CLASS__ . '.' . __FUNCTION__ .
            ' requires the data param to contain a user key.');
        }

        // Contact data check
        $contactDataExists = array_key_exists('contact', $data) && $contactId;

        // If contact key exists
        if ($contactDataExists) {
            $contact = $data['contact'];
        }

        // Escape tuples 
        $dbDataHelper = $this->getDbDataHelper();
        $user = $dbDataHelper->escapeTuple($user);
        $contact = $dbDataHelper->escapeTuple($contact);
        $userContactRel = array(
            'email' => $contact['email'],
            'screenName' => $user['screenName']);

        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create contact
            if (isset($contact)) {
                $this->getContactTable()->udpate($contactId, $contact);
            }

            // Create user
            $this->getUserTable()->update($id, $user);

            // Create user contact rel
            $this->getUserContactRelTable()->insert($userContactRel);

            // Commit and return true
            $conn->commit();
            $retVal = true;
        } catch (\Exception $e) {
            $conn->rollback();
            $retVal = false;
        }
        return $retVal;
    }

    /**
     * Deletes a user and depends on RDBMS triggers and cascade rules to delete
     * it's related tables (contact and user contact rels)
     * @param int $id
     * @return boolean
     */
    public function deleteUser($id) {
        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create user
            $this->getUserTable()->delete(array('user_id' => $id));

            // Commit and return true
            $conn->commit();
            $retVal = true;
        } catch (\Exception $e) {
            $conn->rollback();
            $retVal = false;
        }
        return $retVal;
    }

    /**
     * Gets a user by id
     * @param integer $id
     * @return mixed array | boolean
     */
    public function getById($id) {
        return $this->read(array(
                    'fetchMode' => self::FETCH_FIRST_ITEM,
                    'where' => array('user.user_id' => $id)));
    }

    /**
     * Fetches a user by screen name
     * @param type $screenName
     * @return mixed array | boolean
     */
    public function getByScreenName($screenName) {
        return $this->read(array(
                    'fetchMode' => self::FETCH_FIRST_ITEM,
                    'where' => array('user.screenName' => $screenName)));
    }

    /**
     * Gets a user by email
     * @param string $email
     * @return mixed array | boolean
     */
    public function getByEmail($email) {
        return $this->read(array(
                    'fetchMode' => self::FETCH_FIRST_ITEM,
                    'where' => array('contact.email' => $email)));
    }

    /**
     * Returns our pre-prepared select statement 
     * for our term taxonomy model
     * @todo select should include:
     *      parent_name
     *      parent_alias
     *      taxonomy_name
     * @return Zend\Db\Sql\Select
     */
    public function getSelect($sql = null) {
        $sql = $sql !== null ? $sql : $this->getSql();
        $select = $sql->select();
        // @todo implement return values only for current role level
        return $select
            ->from(array('user' => $this->getUserTable()->table))
            ->join(array('contact' => $this->getContactTable()->table),
                    'contact.contact_id=user.contact_id');
    }

    public function getUserTable() {
        if (empty($this->userTable)) {
            $locator = $this->getServiceLocator();
            $this->userTable = $locator->get('Edm\Db\Table\UserTable');
            $this->userTable->setServiceLocator($locator);
        }
        return $this->userTable;
    }

    public function getContactTable() {
        if (empty($this->contactTable)) {
            $locator = $this->getServiceLocator();
            $this->contactTable = $locator->get('Edm\Db\Table\ContactTable');
            $this->contactTable->setServiceLocator($locator);
        }
        return $this->contactTable;
    }

    public function getUserContactRelTable() {
        if (empty($this->userContactRelTable)) {
            $feature = new FeatureSet();
            $feature->addFeature(new GlobalAdapterFeature());
            $this->userContactRelTable =
                    new \Zend\Db\TableGateway\TableGateway(
                            'user_contact_relationships', $this->getServiceLocator()
                            ->get('Zend\Db\Adapter\Adapter'), $feature);
        }
        return $this->userContactRelTable;
    }

    /**
     * @param string $email
     * @return boolean 
     */
    public function checkUserEmailExists($email) {
        $rslt = $this->getUserContactRelTable()->select()
                        ->where(array('email' => $email))->query()->fetchAll();
        if (empty($rslt)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @param string $screenName
     * @return boolean 
     */
    public function checkScreenNameExists($screenName) {
        $rslt = $this->userContactRelTable->select()
                        ->where('screenName=?', $screenName)
                        ->query()->fetchAll();
        if (!empty($rslt)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns an encoded password
     * @param String $password
     * @return alnum md5 hash
     */
    public function encodePassword($password) {
        return hash('sha256', self::SALT . $password . self::PEPPER);
    }

    /**
     * Returns activation key for user activation
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @return string
     */
    public function generateActivationKey($firstName, $lastName, $email) {
        return hash('md5', self::SALT . time() .
                uniqid($firstName) .
                uniqid($lastName) .
                uniqid($email) .
                self::PEPPER);
    }

    /**
     * Compares activation key to generated one.
     * @param string $key
     * @param array $user
     * @return boolean
     */
    public function is_activationKeyValid($key, $user) {
        return $key === $this->generateActivationKey(
                        $user->firstName, $user->lastName, $user->email);
    }

    /**
     * Generates short unique ids
     * @see http://stackoverflow.com/questions/307486/short-unique-id-in-php 
     *      answer 4
     * @param int $len default 8
     * @param string $seed
     * @return string 
     */
    public function gen_uuid($len = 8, $seed = self::TOKEN_SEED) {
        $hex = md5(self::SALT . $seed . self::PEPPER . uniqid("", true));

        $pack = pack('H*', $hex);

        $uid = base64_encode($pack);        // max 22 chars

        $uid = preg_replace("/[^A-Za-z0-9]/", "", $uid);    // mixed case
        //$uid = ereg_replace("[^A-Z0-9]", "", strtoupper($uid));    // uppercase only

        if ($len < 4)
            $len = 4;
        if ($len > 128)
            $len = 128;                       // prevent silliness, can remove

        while (strlen($uid) < $len)
            $uid = $uid . $this->gen_uuid(22);     // append until length achieved

        return substr($uid, 0, $len);
    }

}