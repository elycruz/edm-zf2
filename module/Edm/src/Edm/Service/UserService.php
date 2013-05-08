<?php

namespace Edm\Service;

use Edm\Service\AbstractService,
    Edm\Model\User,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature,
    Zend\Authentication\Adapter\DbTable,
    Zend\Stdlib\DateTime;

/**
 * @author ElyDeLaCruz
 */
class UserService extends AbstractService 
implements \Edm\UserAware,
            \Edm\Db\CompositeDataColumnAware {
    
    use \Edm\UserAwareTrait,
        \Edm\Db\CompositeDataColumnAwareTrait;

    protected $userTable;
    protected $contactTable;
    protected $userContactRelTable;
    protected $resultSet;
    protected $screenNameLength = 8;
    protected $notAllowedForUpdate = array(
        'activationKey',
        'registeredDate',
        'registeredBy',
        'contact_id',
        'user_id',
        'type'
    );

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
     * @todo make our service use model objects for CRUD operations; I.e.
     * $service->createItem (array $data) should be 
     * $service->createItem (Model $model);
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

        // If no contact key
        if (!array_key_exists('email', $data['contact'])) {
            throw new Exception(__CLASS__ . '.' . __FUNCTION__ .
            ' requires the data param to contain a contact key with an email key.');
        }

        // @todo use only arrays to eliminate multi casting variables multiple
        // times within a function
        $user = (object) $data['user'];
        $contact = (object) $data['contact'];
        $key = isset($user->activationKey) ? $user->activationKey : '';
        $userKeyValid = $this->is_activationKeyValid($key, $contact);

        // If no screen name generate one
        if (empty($user->screenName)) {
            $user->screenName = $this->generateUniqueScreenName();
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

        // If no access group
        if (empty($user->accessGroup)) {
            $user->accessGroup = 'cms-manager';
        }

        // If user has a password
        if (!empty($user->password)) {
            $user->password = $this->encodePassword($user->password);
        }

        // Make sure these are not set
        unset($contact->contact_id);
        unset($contact->name);

        // Remove parent id if not valid
        if (isset($contact->parent_id) && !is_numeric($contact->parent_id)) {
            unset($contact->parent_id);
        }

        // If no contact.type set contact.type to "user"
        if (empty($contact->type)) {
            $contact->type = 'user';
        }

        // Contact params default value
        if (empty($contact->userParams)) {
            $contact->userParams = '';
        }

        // Contact description default value
        if (empty($contact->description)) {
            $contact->description = '';
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
            $cleanUser['contact_id'] =
                    $this->getContactTable()->createItem($cleanContact);

            // Create user
            $retVal = $this->getUserTable()->createItem($cleanUser);

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
     * @param int $contactId
     * @param array $data
     * @return boolean
     * @throws Exception
     */
    public function updateUser($id, $contactId, array $data) {
        // If no user key
        if (!array_key_exists('user', $data)) {
            throw new Exception(__CLASS__ . '.' . __FUNCTION__ .
            ' requires the data param to contain a user key.');
        }

        // Escape tuples 
        $dbDataHelper = $this->getDbDataHelper();
        $user = $dbDataHelper->escapeTuple($this->ensureOkForUpdate($data['user']));
        $contact = $dbDataHelper->escapeTuple($this->ensureOkForUpdate($data['contact']));

//        // Contact data check
//        $contactDataExists = array_key_exists('contact', $data);
//        
//        // If contact key exists
//        if ($contactDataExists) {
//            $contactData = $this->ensureOkForUpdate($data['contact']);
//            $originalData = array_key_exists('originalContact', $data) ? 
//                    $this->ensureOkForUpdate($data['originalContact']) : array();
//
//            // Difference in data
//            $diff = array_diff_assoc($contactData, $originalData);
//
//            // Compare original contact data and new contact data
//            // If data has not changed don't do anything for contact
//            if (count($diff) > 0) {
//                foreach ($diff as $key => $val) {
//                    if (empty($val)) {
//                        unset($diff[$key]);
//                    }
//                }
//                if (count($diff) > 0) {
//                    $contact = $dbDataHelper->escapeTuple($diff);
//                }
//            }
//        }
        // If password encode it
        if (!empty($user['password'])) {
            $user['password'] = $this->encodePassword($user['password']);
        }

        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        try {

            // Update contact
            if (isset($contact)) {
                $this->getContactTable()->update($contact, array('contact_id' => $contactId));
            }

            // Update user
            $this->getUserTable()->update($user, array('user_id' => $id), $user);

            // Commit and return true
            $conn->commit();
            $retVal = true;
        } catch (\Exception $e) {
            $conn->rollback();
            $retVal = $e;
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
            $retVal = $e;
        }
        return $retVal;
    }

    /**
     * Log user in and validate them by (email|screenName) and password
     * @param User $user
     * @param string $credentialColumn default 'screenName'
     * @return boolean
     */
    public function loginUser(User $user, $credentialColumn = 'screenName') {
        // Encode password
        $password = $this->encodePassword($user->password);

        // Get auth adapater
        $authService = $this->getAuthService();
        
        // Set auth type
        $authAdapter = new DbTable(
                        $this->getDb(), 
                        $this->getUserTable()->table, $credentialColumn, 'password');
        
        // Set preliminaries before check
        $authAdapter->setIdentity($user->screenName);
        $authAdapter->setCredential($password);
        $authAdapter->getDbSelect()->where(array('status' => 'activated'));
        $rslt = $authService->authenticate($authAdapter);

        // Check if credentials are valid
        if ($rslt->isValid()) {
            // store the username, first and last names of the user
            $storage = $authService->getStorage();
            $storage->write($authAdapter->getResultRowObject(array(
                                'user_id', $credentialColumn, 'lastLogin',
                                'role', 'registeredDate')));

            // Update user lastLogin
            $this->getUserTable()
                    ->updateLastLoginForId($authService->getIdentity()->user_id);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Clears the user token
     */
    public function logoutUser() {
        $auth = $this->getAuthService();
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
            return true;
        }
        return false;
    }

    /**
     * Gets a user by id
     * @param integer $id
     * @param integer $fetchMode
     * @return mixed array | boolean
     */
    public function getById($id, $fetchMode = AbstractService::FETCH_FIRST_AS_ARRAY) {
        return $this->read(array(
                    'fetchMode' => $fetchMode,
                    'where' => array('user.user_id' => $id)));
    }

    /**
     * Fetches a user by screen name
     * @param string $screenName
     * @param int $fetchMode
     * @return mixed array | boolean
     */
    public function getByScreenName($screenName, $fetchMode = AbstractService::FETCH_FIRST_AS_ARRAY) {
        return $this->read(array(
                    'fetchMode' => $fetchMode,
                    'where' => array('user.screenName' => $screenName)));
    }

    /**
     * Gets a user by email
     * @param string $email
     * @return mixed array | boolean
     */
    public function getByEmail($email, $fetchMode = AbstractService::FETCH_FIRST_AS_ARRAY) {
        return $this->read(array(
                    'fetchMode' => $fetchMode,
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
                        ->join(array('contact' => $this->getContactTable()->table), 'contact.contact_id=user.contact_id');
    }

    public function getUserTable() {
        if (empty($this->userTable)) {
            $locator = $this->getServiceLocator();
            $this->userTable = $locator->get('Edm\Db\Table\UserTable');
        }
        return $this->userTable;
    }

    public function getContactTable() {
        if (empty($this->contactTable)) {
            $this->contactTable = $this->getServiceLocator()
                    ->get('Edm\Db\Table\ContactTable');
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
     * Checks if an email already exists for a user
     * @param string $email
     * @return boolean 
     */
    public function checkEmailExistsInDb($email) {
        $rslt = $this->getUserContactRelTable()->select(
                        array('email' => $email))->current();
        if (empty($rslt)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Checks if screen name exists
     * @param string $screenName
     * @return boolean 
     */
    public function checkScreenNameExistsInDb($screenName) {
        $rslt = $this->getUserContactRelTable()->select(
                        array('screenName' => $screenName))->current();
        if (!empty($rslt)) {
            return true;
        }
        return false;
    }

    /**
     * Remove any empty keys and ones in the not ok for update list
     * @param array $data
     * @return array
     */
    public function ensureOkForUpdate(array $data) {
        foreach ($this->notAllowedForUpdate as $key) {
            if (array_key_exists($key, $data) ||
                    (array_key_exists($key, $data) && empty($data[$key]))) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    /**
     * Returns a unique screen name with length of "screen name length"
     * @return string
     */
    public function generateUniqueScreenName() {
        $screenName = '';
        do {
            $screenName = $this->gen_uuid($this->screenNameLength);
        } while ($this->checkScreenNameExistsInDb($screenName));
        return $screenName;
    }

    /**
     * Returns an encoded password
     * @param String $password
     * @return alnum md5 hash
     */
    public function encodePassword($password) {
        return hash('sha256', EDM_SALT . $password . EDM_PEPPER);
    }

    /**
     * Returns activation key for user activation
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @return string
     */
    public function generateActivationKey($firstName, $lastName, $email) {
        return hash('md5', EDM_SALT . time() .
                uniqid($firstName) .
                uniqid($lastName) .
                uniqid($email) .
                EDM_PEPPER);
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
    public function gen_uuid($len = 8, $seed = EDM_TOKEN_SEED) {
        $hex = md5(EDM_SALT . $seed . EDM_PEPPER . uniqid("", true));

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