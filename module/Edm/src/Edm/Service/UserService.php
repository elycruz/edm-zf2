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
     * @param UserProto $user
     * @return \Exception|int
     * @throws UnqualifiedDataException
     */
    public function create(UserProto $user) {
        // Get today's date
        $today = new \DateTime();

        // Creation timestamp
        $timestamp = $today->getTimestamp();

        // Get contact data
        $contact = $user->getContactProto();

        // If no screen name generate one
        if (!$user->has('screenName')) {
            $user->screenName = $this->generateUniqueScreenName();
        }

        // If user has a password
        if (!empty($user->password)) {
            $user->password = $this->encodePassword($user->password);
        }

        // Set user activation key
        $user->activationKey =
            $this->generateActivationKey(
                $user->screenName,
                $contact->email,
                $timestamp
            );

        // Set user activation key created date
        $user->activationKeyCreatedDate = $timestamp;

        // Remove parent id if not valid
        if (isset($contact->parent_id) && !is_numeric($contact->parent_id)) {
            unset($contact->parent_id);
        }

        // Set user params for contact if they are empty
        if (!isset($contact->userParams) || !is_string($contact->userParams)) {
            $contact->userParams = '';
        }

        // Get db data helper
        $dbDataHelper = $this->getDbDataHelper();

        // Escape user data
        $user = $dbDataHelper->escapeTuple($user);

        // Escape contact data
        $contact = $dbDataHelper->escapeTuple($contact);

        // User contact rel
        $userContactRel = array(
            'email' => $contact->email,
            'screenName' => $user->screenName);

        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        // Try to insert user data
        try {
            // Create contact
            $this->getContactTable()->insert($contact->toArray());

            // Insert date info
            $this->getDateInfoTable()->insert(['createdDate' => $timestamp]);

            // Get date_info_id for post
            $user['date_info_id'] = $driver->getLastGeneratedValue();

            // Create user
            $this->getUserTable()->insert($user->toArray());

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

    /**
     * @param int|string $id
     * @param array $data
     * @param array $originalData
     * @param bool $escapeOriginalData - Optional.  Default `false`.
     * @return bool|\Exception
     * @throws \Exception
     */
    public function update ($id, $data, $originalData, $escapeOriginalData = false) {
//        var_dump(func_get_args());
        // If no user key
        if (!array_key_exists('user', $data) || !array_key_exists('user', $originalData)) {
            throw new \Exception(__CLASS__ . '.' . __FUNCTION__ .
                ' requires the data and original data params to contain a user key.');
        }

        // Get today's date
        $today = new \DateTime();

        // Creation timestamp
        $timestamp = $today->getTimestamp();

        // Escape tuples
        $dbDataHelper = $this->getDbDataHelper();
        $data = $dbDataHelper->escapeTuple($data);
        $user = $data['user'];

        // Check whether to escape original data or not
        if ($escapeOriginalData) {
            $originalData = $dbDataHelper->escapeTuple($originalData);
        }

        // Get original user data
        $originalUser = $originalData['user'];
        $originalContact = $originalData['contact'];

        // Set flags for whether to update contact table
        $updateContactTable = false;

        // Track whether email changed
        $emailChanged = false;

        // Track whether screen name changed
        $screenNameChanged = isset($user['screenName']) &&
            $originalUser['screenName'] !== $user['screenName'];

        // Get contact data if necessary
        if (isset($data['contact']) && isset($originalData['contact'])) {
            $contact = $data['contact'];
            $originalContact = $originalData['contact'];
            $emailChanged = isset($contact['email']) && $contact['email'] !== $originalContact['email'];
            $updateContactTable = true;
        }

        // If email changed require user to activate it via an email by issuing new activation key
        if ($emailChanged) {
            // Set user activation key
            $user['activationKey'] =
                $this->generateActivationKey(
                    $user['screenName'],
                    $contact['email'],
                    $timestamp
                );

            // Set user activation key created date
            $user['activationKeyCreatedDate'] = $timestamp;
        }

        // If password encode it
        if (!empty($user['password'])) {
            $user['password'] = $this->encodePassword($user['password']);
        }

        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        // Try to update user
        try {

            // Update contact if necessary
            if ($updateContactTable) {
                $contactUpdateOptions = ['email' => $originalContact['email']];
                $this->getContactTable()->update($contact, $contactUpdateOptions);
            }

            // Update user
            $this->getUserTable()->update($user, array('user_id' => $id), $user);

            // Resolve data for contact user rel table if necessary
            if ($screenNameChanged || $emailChanged) {
                $contactUserRelData = [];
                if ($screenNameChanged) {
                    $contactUserRelData['screenName'] = $user['screenName'];
                }
                if ($emailChanged) {
                    $contactUserRelData['email'] = $contact['email'];
                }
                $this->getContactUserRelTable()->update($contactUserRelData, [
                    'screenName' => $originalUser['screenName'],
                    'email' => $originalContact['email']
                ]);
            }

            // Update date info table
            $this->getDateInfoTable()->update([
                    'lastUpdated' => $timestamp,
                    'lastUpdatedById' => 0
                ], [
                    'date_info_id' => $originalUser['date_info_id']
                ]);

            // Commit and return true
            $conn->commit();

            // Set return value
            $retVal = true;
        }
        catch (\Exception $e) {
            $conn->rollback();
            $retVal = $e;
        }

        // Return value
        return $retVal;
    }

    /**
     * @param UserProto $userProto
     * @return bool|\Exception
     * @throws UnqualifiedDataException
     */
    public function delete (UserProto $userProto) {
        // Get db connection
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin db transaction
        $conn->beginTransaction();

        // Try to delete user
        try {
            // Delete entries for user id $id
            $this->getUserTable()->delete(['user_id' => $userProto->user_id]);
            $this->getContactUserRelTable()->delete(['screenName' => $userProto->screenName]);
            $this->getContactTable()->delete(['email' => $userProto->getContactProto()->email]);

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
    public function checkIfEmailExistsInDb($email) {
        $rslt = $this->getContactUserRelTable()->select(
            array('email' => $email))->current();
        return !empty($rslt);
    }

    /**
     * Checks if screen name exists
     * @param string $screenName
     * @return boolean
     */
    public function checkIfScreenNameExistsInDb($screenName) {
        $rslt = $this->getContactUserRelTable()
            ->select(array('screenName' => $screenName))->current();
        return !empty($rslt);
    }

    /**
     * Gets a user by id
     * @param integer $id
     * @return mixed array | boolean
     */
    public function getUserById($id) {
        return $this->read([
            'where' => [$this->getUserTable()->alias .  '.user_id' => $id]
        ])->current();
    }

    /**
     * Fetches a user by screen name
     * @param string $screenName
     * @return mixed array | boolean
     */
    public function getUserByScreenName($screenName) {
        return $this->read([
            'where' => [$this->getUserTable()->alias .  '.screenName' => $screenName]
        ])->current();
    }

    /**
     * Gets a user by email
     * @param string $email
     * @return mixed array | boolean
     */
    public function getUserByEmail($email) {
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
    public function updateLastLoginForUserById ($userId) {
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
    public function logUserIn(UserProto $user,
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
    public function logUserOut() {
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
     * @param string $screenName
     * @param string $email
     * @param int $timestamp
     * @return boolean
     */
    public function isActivationKeyValid($key, $screenName, $email, $timestamp = null) {
        return $key === $this->generateActivationKey($screenName, $email, $timestamp);
    }

    /**
     * Returns 32 character length activation key for user activation
     * @param string $screenName
     * @param string $email
     * @param string $timestamp
     * @param string $salt - Default `EDM_SALT`
     * @param string $pepper - Default `EDM_PEPPER`
     * @return string
     */
    public function generateActivationKey($screenName, $email, $timestamp = null, $salt = EDM_SALT, $pepper = EDM_PEPPER) {
        $timestamp = !isset($timestamp) ? time() : $timestamp;
        return hash('md5', $salt . $timestamp . $screenName . $email . $pepper);
    }

    /**
     * Returns a unique screen name with length of "screen name length"
     * @param int $screenNameLength
     * @return string
     */
    public function generateUniqueScreenName($screenNameLength = 8) {
        do {
            $screenName = $this->generateUUID($screenNameLength);
        } while ($this->checkIfScreenNameExistsInDb($screenName));
        return $screenName;
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


    public function normalizeCrudData ($nestedDataArray) {

    }

}
