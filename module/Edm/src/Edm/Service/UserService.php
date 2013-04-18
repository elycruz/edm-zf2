<?php

namespace Edm\Service;

use Edm\Service\AbstractService,
    Edm\Service\TermTaxonomyServiceAware,
    Edm\Service\TermTaxonomyServiceAwareTrait,
    Edm\Model\User,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql;

/**
 * @author ElyDeLaCruz
 */
class UserService extends AbstractService implements 
        TermTaxonomyServiceAware {
    
    use TermTaxonomyServiceAwareTrait;

    protected $userTable;
    protected $contactTable;
    protected $userContactRelTable;
    protected $termTaxService;
    protected $resultSet;

    public function __construct() {
        $this->sql = new Sql($this->getDb());
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype(new User());
    }

    public function createUser (array $data) {
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
        
        // If no api key and activation key is required generate
        if (empty($user->activationKey)) {
            $user->activationKey = $this->generateActivationKey(
                    $contact->firstName, 
                    $contact->lastName, 
                    $contact->email);
        }
        
        // Set status to "pending-activation"
        if ($user->status === 'pending-activation') {
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
        
        // Set registeredDate
        $user->registeredDate = new Zend\Stdlib\DateTime();
        
        // Escape tuples 
        $dbDataHelper = $this->getDbDataHelper();
        $user =  $dbDataHelper->escapeTuple((array) $user);
        $contact = $dbDataHelper->escapeTuple((array) $contact);
        $userContactRel = array(
            'email' => $contact['email'],
            'screenName' => $user['screenName']);
        
        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();
        
        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create contact
            $user['contact_id'] = 
                    $this->getContactTable()->createItem($contact);
            
            // Create user
            $this->getUserTable()->createItem($user);

            // Create user contact rel
            $this->getUserContactRelTable()->insert($userContactRel);
            
            // Commit and return true
            $conn->commit();
            $retVal = true;
        }
        catch (\Exception $e) {
            $conn->rollback();
            $retVal = false;
        }
        return $retVal;
    }
    
    public function updateUser ($user_id, $data) {
        // If no user key
        if (!array_key_exists('user', $data)) {
            throw new Exception(__CLASS__ . '.' . __FUNCTION__ . 
                    ' requires the data param to contain a user key.');
        }
        
        // If contact key exists
        if (array_key_exists('contact', $data)) {
            $contact = (object) $data['contact'];
        }
        
        // @todo use only arrays to eliminate multi casting variables multiple
        // times within a function
        $user = (object) $data['user'];
        
        // If no activation key generate
        if (!empty($user->activationKey) 
            && !$this->is_validActivationKey($user->activationKey)) {
            $user->activationKey = $this->generateActivationKey(
                    $contact->firstName, 
                    $contact->lastName, 
                    $contact->email);
        }
        
        // Set status to "pending-activation"
        if (empty($user->status)) {
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
        
        // Set registeredDate
        $user->registeredDate = new Zend\Stdlib\DateTime();
        
        // Escape tuples 
        $dbDataHelper = $this->getDbDataHelper();
        $user =  $dbDataHelper->escapeTuple((array) $user);
        $contact = $dbDataHelper->escapeTuple((array) $contact);
        $userContactRel = array(
            'email' => $contact['email'],
            'screenName' => $user['screenName']);
        
        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();
        
        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create contact
            $user['contact_id'] = 
                    $this->getContactTable()->createItem($contact);
            
            // Create user
            $this->getUserTable()->createItem($user);

            // Create user contact rel
            $this->getUserContactRelTable()->insert($userContactRel);
            
            // Commit and return true
            $conn->commit();
            $retVal = true;
        }
        catch (\Exception $e) {
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
        $sql = $this->getSql();
        $select = $this->getSelect($sql)->where(array('user.user_id' => $id));
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }

    /**
     * Gets a user by screen name
     * @param string $screenName
     * @return mixed array | boolean
     */
    public function getByScreenName($screenName) {
        $sql = $this->getSql();
        $select = $this->getSelect($sql)->where(
                array('user.screenName' => $screenName));
        return $this->resultSet->initialize(
                        $sql->prepareStatementForSqlObject($select)->execute()
                )->current();
    }
    
    public function getByEmail ($email) {
        $sql = $this->getSql();
        $select = $this->getSelect($sql)->where(
                array('contact.email' => $email));
        return $this->resultSet->initialize(
                        $sql->prepareStatementForSqlObject($select)->execute()
                )->current();
        
    }

    /**
     * Read term taxonomies
     * @param mixed $options
     */
    public function read($options = null) {
        // Normalize/get options object and seed it with default select params
        $options = $this->seedOptionsForSelect(
                $this->normalizeMethodOptions($options));

        // Get results
        $rslt = $this->resultSet->initialize(
                $options->sql->prepareStatementForSqlObject(
                        $options->select
                )->execute());

        return $this->fetchFromResult($rslt, $options->fetchMode);
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
                        'user.contact_id=contact.contact_id');
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
            $this->userContactRelTable = 
                new TableGateway('user_contact_relationships', 
                        $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
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
        }
        else {
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
        }
        else {
            return false;
        }
    }
    
      /**
     * Returns an encoded password
     * @param String $password
     * @return alnum md5 hash
     */
    public function encodePassword($password) {
        return hash('sha256', SALT . $password . PEPPER);
    }

    /**
     * Returns activation key for user activation
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @return string
     */
    public function generateActivationKey($firstName, $lastName, $email) {
        return hash('md5', SALT . time() .
                uniqid($firstName) .
                uniqid($lastName) .
                uniqid($email) .
                PEPPER);
    }
    
        /**
     * Generates short unique ids
     * @see http://stackoverflow.com/questions/307486/short-unique-id-in-php 
     *      answer 4
     * @param int $len default 8
     * @param string $seed
     * @return string 
     */
    public function gen_uuid($len = 8, $seed = DEFAULT_TOKEN_SEED) {
        $hex = md5(SALT . $seed . PEPPER . uniqid("", true));

        $pack = pack('H*', $hex);

        $uid = base64_encode($pack);        // max 22 chars

        $uid = ereg_replace("[^A-Za-z0-9]", "", $uid);    // mixed case
        //$uid = ereg_replace("[^A-Z0-9]", "", strtoupper($uid));    // uppercase only

        if ($len < 4)
            $len = 4;
        if ($len > 128)
            $len = 128;                       // prevent silliness, can remove

        while (strlen($uid) < $len)
            $uid = $uid . gen_uuid(22);     // append until length achieved

        return substr($uid, 0, $len);
    }

}