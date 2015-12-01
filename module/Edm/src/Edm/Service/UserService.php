<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/25/2015
 * Time: 1:42 AM
 */

namespace Edm\Service;

use Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql,
    Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter,
    Edm\Db\ResultSet\Proto\UserProto,
    Edm\Db\TableGateway\DateInfoTableAware,
    Edm\Db\TableGateway\DateInfoTableAwareTrait,
    Edm\Hasher\Pbkdf2Hasher,
    Edm\UserAware,
    Edm\UserAwareTrait;

class UserService extends AbstractCrudService
    implements DateInfoTableAware, UserAware {

    use DateInfoTableAwareTrait,
        UserAwareTrait;

    protected $userTable;
    protected $contactTable;
    protected $userContactRelTable;
    protected $hasher;

    public function __construct () {
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype(new UserProto());
    }

    /**
     * @param array $data
     * @return \Exception|int
     * @throws UnqualifiedDataException
     */
    public function create(array $data) {
        if (!isset($data['user']) || !isset($data['contact'])) {
            throw new UnqualifiedDataException(__CLASS__ . '->' . __FUNCTION__ . ' expects $data
            to include both a "user" and a "contact" key.  Keys found: '. implode(array_keys($data), ', '));
        }

        // Get data
        $contact = $data['contact'];
        $user = $data['user'];

        // If no screen name generate one
        if (empty($user['screenName'])) {
            $user['screenName'] = $this->generateUniqueScreenName();
        }

        // If user has a password
        if (!empty($user['password'])) {
            $user['password'] = $this->encodePassword($user['password']);
        }

        // Remove parent id if not valid
        if (isset($contact['parent_id']) && !is_numeric($contact['parent_id'])) {
            unset($contact['parent_id']);
        }

        $key = isset($user['activationKey']) ? $user['activationKey'] : '';
        $userKeyValid = $this->isActivationKeyValid($key, $contact);

        // If no api key and activation key is required generate
        if (!$userKeyValid) {
            $user['activationKey'] = $this->generateActivationKey(
                $contact['firstName'], $contact['lastName'], $contact['email']);
        }

        if (empty($contact['userParams'])) {
            $contact['userParams'] = '';
        }

        // Get db data helper
        $dbDataHelper = $this->getDbDataHelper();

        // Escape user data
        $user = $dbDataHelper->escapeTuple($user);

        // Escape contact data
        $contact = $dbDataHelper->escapeTuple($contact);

        // User contact rel
        $userContactRel = array(
            'email' => $contact['email'],
            'screenName' => $user['screenName']);

        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        // Try to insert user data
        try {
            // Create contact
            $this->getContactTable()->insert($contact);

            // Insert date info
            $today = new \DateTime();
            $this->getDateInfoTable()->insert(['createdDate' => $today->getTimestamp()]);

            // Get date_info_id for post
            $user['date_info_id'] = $driver->getLastGeneratedValue();

            // Create user
            $this->getUserTable()->insert($user);

            // Get last generated id
            $retVal = (int) $driver->getLastGeneratedValue();

            // Create user contact rel
            $this->getContactUserRelTable()->insert($userContactRel);

            // Commit and return true
            $conn->commit();
        }
        catch (\Exception $e) {
            $conn->rollback();
            $retVal = $e;
        }
        return $retVal;
    }

    public function update ($id, $data) {}

    /**
     * @param int $id
     * @return bool|\Exception
     * @throws UnqualifiedDataException
     */
    public function delete ($id) {
        // Get db connection
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin db transaction
        $conn->beginTransaction();

        // Try to delete user
        try {
            // Fetch existing user
            $existingUserRow = $this->getById($id);

            // Throw an error if user doesn't exist
            if (empty($existingUserRow)) {
                throw new UnqualifiedDataException ('Failed to delete user with id "' . $id . '".  User doesn\'t exist in database.');
            }

            // Delete entries for user id $id
            $this->getUserTable()->delete(['user_id' => $id]);
            $this->getContactUserRelTable()->delete(['screenName' => $existingUserRow->screenName]);
            $this->getContactTable()->delete(['email' => $existingUserRow->getContactProto()->email]);

            // Commit changes
            $conn->commit();

            // Return success
            $retVal = true;
        }
        // Catch and return any exceptions
        catch (\Exception $e) {
            // Roll back changes
            $conn->rollback();

            // Capture and return error
            $retVal = $e; //false;
        }

        // Return return value
        return $retVal;
    }

    /**
     * Checks if an email already exists for a user
     * @param string $email
     * @return boolean
     */
    public function checkEmailExistsInDb($email) {
        $rslt = $this->getContactUserRelTable()->select(
            array('email' => $email))->current();
        return !empty($rslt);
    }

    /**
     * Checks if screen name exists
     * @param string $screenName
     * @return boolean
     */
    public function checkScreenNameExistsInDb($screenName) {
        $rslt = $this->getContactUserRelTable()
            ->select(array('screenName' => $screenName))->current();
        return !empty($rslt);
    }

    /**
     * Gets a user by id
     * @param integer $id
     * @return mixed array | boolean
     */
    public function getById($id) {
        return $this->read([
            'where' => [$this->getUserTable()->alias .  '.user_id' => $id]
        ])->current();
    }

    /**
     * Fetches a user by screen name
     * @param string $screenName
     * @return mixed array | boolean
     */
    public function getByScreenName($screenName) {
        return $this->read([
            'where' => [$this->getUserTable()->alias .  '.screenName' => $screenName]
        ])->current();
    }

    /**
     * Gets a user by email
     * @param string $email
     * @return mixed array | boolean
     */
    public function getByEmail($email) {
        return $this->read([
            'where' => [$this->getContactTable()->alias . '.email' => $email]
        ])->current();
    }

    /**
     * Returns our pre-prepared select statement
     * for our term taxonomy model
     * @param Sql $sql
     * @param array $options
     * @todo select should include: [parent_name, parent_alias, taxonomy_name]
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect(Sql $sql = null, array $options = null) {
        $sql = $sql !== null ? $sql : $this->getSql();
        $select = $sql->select();
        $hasOptions = isset($options);
        $includeSensitiveUserColumns = $hasOptions && isset($options['includeSensitiveUserColumns']) ?
            $options['includeSensitiveUserColumns'] : false;

        // Get required tables for result set
        $userTable = $this->getUserTable();
        $contactUserRelTable = $this->getContactUserRelTable();
        $contactTable = $this->getContactTable();
        $dateInfoTable = $this->getDateInfoTable();

        // User table columns
        $userTableColumns = [
            'user_id', 'role', 'accessGroup', 'status', 'lastLogin', 'date_info_id'
        ];

        // Conditionally include sensitive `user` table columns
        if ($includeSensitiveUserColumns) {
            $userTableColumns[] = 'password';
            $userTableColumns[] = 'activationKey';
        }

        // @todo implement return values only for current role level
        return $select
            // User Contact Rel Table
            ->from(array($contactUserRelTable->alias => $contactUserRelTable->table))

            // User Table
            ->join(array($userTable->alias => $userTable->table),
                $userTable->alias . '.screenName=' . $contactUserRelTable->alias . '.screenName',
                $userTableColumns)

            // Contact Table
            ->join(array($contactTable->alias => $contactTable->table),
                $contactTable->alias . '.email=' . $contactUserRelTable->alias . '.email',
                array(
                    'contact_id', 'altEmail', 'name',
                    'type', 'firstName', 'middleName', 'lastName',
                    'userParams'))

            // Date Info Table
            ->join(array($dateInfoTable->alias => $dateInfoTable->table),
                $userTable->alias . '.date_info_id=' . $dateInfoTable->alias . '.date_info_id', array(
                    'createdDate', 'createdById', 'lastUpdated', 'lastUpdatedById'));
    }

    /**
     * @param int $userId
     * @return UserService
     */
    public function updateLastLoginById ($userId) {
        $today = new \DateTime();
        $this->update($userId, array('lastLogin' => $today->getTimestamp()));
        return $this;
    }

    /**
     * Log a user in and validate them by identity column and credential column.
     * @param UserProto $user
     * @param string $identityColumn - Default 'screenName'.
     * @param string $credentialColumn default 'password'
     * @return boolean
     */
    public function loginUser(UserProto $user,
                              $identityColumn = 'screenName',
                              $credentialColumn = 'password')
    {
        // Get auth adapter
        $authService = $this->getAuthService();

        // Set auth type
        $authAdapter = new CallbackCheckAdapter(
            $this->getDb(),
            $this->getUserTable()->table,
            $identityColumn,
            $credentialColumn,
            function ($a, $b) {
                $hasher = new Pbkdf2Hasher();
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
            $this->updateLastLoginForId($authService->getIdentity()->user_id);
            return true;
        }
        else {
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
     * Returns an encoded password
     * @param string $password
     * @return string - Pbkdf2 hashed password
     */
    public function encodePassword($password) {
        return $this->getHasher()->create_hash($password);
    }

    /**
     * Compares activation key to generated one.
     * @param string $key
     * @param array $contact
     * @return boolean
     */
    public function isActivationKeyValid($key, $contact) {
        return $key === $this->generateActivationKey(
            $contact['firstName'], $contact['lastName'], $contact['email']);
    }

    /**
     * Returns a unique screen name with length of "screen name length"
     * @param int $screenNameLength
     * @return string
     */
    public function generateUniqueScreenName($screenNameLength = 8) {
        do {
            $screenName = $this->generateUUID($screenNameLength);
        } while ($this->checkScreenNameExistsInDb($screenName));
        return $screenName;
    }

    /**
     * Returns 32 character length activation key for user activation
     * @param string $screenName
     * @param string $salt - Default `EDM_SALT`
     * @param string $pepper - Default `EDM_PEPPER`
     * @return string
     */
    public function generateActivationKey($screenName, $salt = EDM_SALT, $pepper = EDM_PEPPER) {
        return hash('md5', $salt . time() . uniqid($screenName) . $pepper);
    }

    /**
     * Generates short unique ids
     * @see http://stackoverflow.com/questions/307486/short-unique-id-in-php
     *      answer 4
     * @param int $len default 8
     * @param string $seed
     * @return string
     */
    public function generateUUID($len = 8, $seed = EDM_TOKEN_SEED) {
        $hex = md5(EDM_SALT . $seed . EDM_PEPPER . uniqid("", true));

        $pack = pack('H*', $hex);

        $uid = base64_encode($pack);        // max 22 chars

        $uid = preg_replace("/[^A-Za-z0-9]/", "", $uid);    // mixed case

        if ($len < 4)
            $len = 4;
        if ($len > 128)
            $len = 128;                       // prevent silliness, can remove

        while (strlen($uid) < $len)
            $uid = $uid . $this->generateUUID(22);     // append until length achieved

        return substr($uid, 0, $len);
    }

    /**
     * Returns our password hasher (pbkdf2 hasher).
     * @return Pbkdf2Hasher
     */
    public function getHasher() {
        if (empty($this->hasher)) {
            $this->hasher = new Pbkdf2Hasher();
        }
        return $this->hasher;
    }

    /**
     * @return \Edm\Db\TableGateway\UserTable
     */
    public function getUserTable() {
        if (empty($this->userTable)) {
            $this->userTable = $this->getServiceLocator()
                ->get('Edm\Db\TableGateway\UserTable');
        }
        return $this->userTable;
    }

    /**
     * @return \Edm\Db\TableGateway\ContactTable
     */
    public function getContactTable() {
        if (empty($this->contactTable)) {
            $this->contactTable = $this->getServiceLocator()
                ->get('Edm\Db\TableGateway\ContactTable');
        }
        return $this->contactTable;
    }

    /**
     * @return \Edm\Db\TableGateway\ContactUserRelTable
     */
    public function getContactUserRelTable() {
        if (empty($this->userContactRelTable)) {
            $this->userContactRelTable = $this->getServiceLocator()
                ->get('Edm\Db\TableGateway\ContactUserRelTable');
        }
        return $this->userContactRelTable;
    }
    
}
