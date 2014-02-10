<?php

namespace Edm\Service;

// Temporarily include hasher
require(implode(DIRECTORY_SEPARATOR, array('CrackStation', 'Pbkdf2_Hasher.php')));

use Edm\Service\AbstractService,
    Edm\Model\User,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature,
    Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter as DbTableWithCallback;

/**
 * @author ElyDeLaCruz
 */
class UserService extends AbstractService implements \Edm\UserAware, \Edm\Db\CompositeDataColumnAware {

    use \Edm\UserAwareTrait,
        \Edm\Db\CompositeDataColumnAwareTrait,
        \Edm\Db\Table\DateInfoTableAwareTrait;

    protected $userTable;
    protected $contactTable;
    protected $userContactRelTable;
    protected $resultSet;
    protected $screenNameLength = 8;
    protected $notAllowedForUpdate = array(
        'activationKey',
        'user_id'
    );

    /**
     * Our password hasher.
     * @var Pbkdf2_Hasher
     */
    protected $hasher;

    public function __construct() {
        $this->sql = new Sql($this->getDb());
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype(new User());
    }

    /**
     * Creates a user and it's constituants 
     *  (contact and user contact relationship)
     * @param User $user
     * @return mixed int | boolean
     * @throws Exception
     * @todo make our service use model objects for CRUD operations; I.e.
     * $service->createItem (array $data) should be 
     * $service->createItem (Model $model);
     */
    public function createUser(User $user) {
        // @todo use only arrays to eliminate multi casting variables multiple
        // times within a function
        $contact = $user->getContactProto();
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

        // If user has a password
        if (!empty($user->password)) {
            $user->password = $this->encodePassword($user->password);
        }

        // Remove parent id if not valid
        if (isset($contact->parent_id) && !is_numeric($contact->parent_id)) {
            unset($contact->parent_id);
        }
        
        // Escape tuples 
        $dbDataHelper = $this->getDbDataHelper();
        $cleanUser = $dbDataHelper->escapeTuple(
                $this->ensureOkForUpdate($user->toArray()));
        $cleanContact = $dbDataHelper->escapeTuple(
                $this->ensureOkForUpdate($contact->toArray()));

        // User contact rel
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

            // Insert date info
            $today = new \DateTime();
            $this->getDateInfoTable()->insert(
                    array('createdDate' => $today->getTimestamp(), 
                          'createdById' => '0'));
            
            // Get date_info_id for post
            $cleanUser['date_info_id'] = $driver->getLastGeneratedValue();
            
            // Create user
            $retVal = $this->getUserTable()->insert($cleanUser);

            // Create user contact rel
            $this->getUserContactRelTable()->insert($userContactRel);
            
            // Commit and return true
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            $retVal = $e;
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
    
    private function _logUserIn (User $user) {
        $un_hashed_password = $user->password;
        $fetchedUser = $this->getByScreenName($user->screenName);
        
        // Validate
        if (is_array($fetchedUser) 
                && count($fetchedUser) > 0
                && !empty($fetchedUser['screenName'])
                && !empty($fetchedUser['password'])) {
                    $validatedUser = new User($fetchedUser);
            $hashed_password = $validatedUser->password;
        }

        // Validate user 
        $rslt = $this->getHasher()
                ->validate_against_hash($un_hashed_password, $hashed_password);
        
        if (!$rslt) {
            return false;
        }
        
        // store the username, first and last names of the user
        $authService = $this->getAuthService();
        $storage = $authService->getStorage();
        $storage->write(array(
                    'user_id' => $validatedUser->user_id,
                    'screeName' => $validatedUser->screenName,
                    'lastLogin' => $validatedUser->lastLogin,
                    'role' => $validatedUser->role
                ));

        // Update user lastLogin
        $this->getUserTable()
                ->updateLastLoginForId($validatedUser->user_id);
        
        return true;

    }

    /**
     * Log user in and validate them by (email|screenName) and password
     * @param User $user
     * @param string $credentialColumn default 'screenName'
     * @todo figure out what to do about authservice and pbkdf2_hasher (maybe compose a new auth service that uses the pbkdf2 hasher to do it's job
     * @return boolean
     */
    public function loginUser(User $user, 
            $identityColumn = 'screenName', $credentialColumn = 'password') {
        
        // Get auth adapater
        $authService = $this->getAuthService();

        // Set auth type
        $authAdapter = new DbTableWithCallback(
                $this->getDb(), 
                $this->getUserTable()->table, 
                'screenName', 
                'password',
                function ($a, $b) {
                    $hasher = new \Pbkdf2_Hasher();
                    return $hasher->validate_against_hash($b, $a);
                });

        // Set preliminaries before check
        $authAdapter->setIdentity($user->screenName);
        $authAdapter->setCredential($user->password);
        $authAdapter->getDbSelect()->where(array('status' => 'activated'));
        $rslt = $authService->authenticate($authAdapter);

        // Check if credentials are valid
        if ($rslt->isValid()) {
            // store the username, first and last names of the user
            $storage = $authService->getStorage();
            $storage->write($authAdapter->getResultRowObject(array(
                        'user_id', $identityColumn, 'lastLogin',
                        'role')));

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
        // @todo make password and activationkey optional via flag
        return $select
            // User Contact Rel Table
            ->from(array('userContactRel' => $this->getUserContactRelTable()->table))

            // User Table
            ->join(array('user' => $this->getUserTable()->table), 
                    'user.screenName=userContactRel.screenName', 
                    array(
                        'user_id', 'password', 'role',
                        'accessGroup', 'status', 'lastLogin',
                        'activationKey', 'date_info_id'))

            // Contact Table
            ->join(array('contact' => $this->getContactTable()->table), 
                    'contact.email=userContactRel.email', 
                    array(
                        'contact_id', 'altEmail', 'name',
                        'type', 'firstName', 'middleName', 'lastName',
                        'userParams'))

            // Date Info Table
            ->join(array('dateInfo' => $this->getDateInfoTable()->table),
                'user.date_info_id=dateInfo.date_info_id', array(
                    'createdDate', 'createdById', 'lastUpdated', 'lastUpdatedById'));
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
            $this->userContactRelTable = new \Zend\Db\TableGateway\TableGateway(
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
        return $this->getHasher()->create_hash($password); //EDM_SALT . $password . EDM_PEPPER);
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

    /**
     * Our password and activation key hasher.
     * @return Pbkdf2_Hasher
     */
    public function getHasher() {
        if (empty($this->hasher)) {
            $this->hasher = new \Pbkdf2_Hasher();
        }
        return $this->hasher;
    }

}
