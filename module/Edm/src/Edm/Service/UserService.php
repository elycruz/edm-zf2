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
    Edm\Db\ResultSet\Proto\UserProto,
    Edm\Db\TableGateway\DateInfoTableAware,
    Edm\Db\TableGateway\DateInfoTableAwareTrait;

class UserService extends AbstractCrudService implements DateInfoTableAware {

    use DateInfoTableAwareTrait;

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
     * @throws \Exception
     */
    public function createUser(array $data) {
        if (!isset($data['user']) || !isset($data['contact'])) {
            throw new \Exception (__CLASS__ . '->' . __FUNCTION__ . ' expects $data
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

        // Escape user data
        $user = $this->escapeTuple($user);

        // Escape contact data
        $contact = $this->escapeTuple($contact);

        // User contact rel
        $userContactRel = array(
            'email' => $contact['email'],
            'screenName' => $user['screenName']);

        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();

        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create contact
            $this->getContactTable()->insert($contact);

            // Insert date info
            $today = new \DateTime();
            $this->getDateInfoTable()->insert(
                array('createdDate' => $today->getTimestamp(),
                    'createdById' => '0'));

            // Get date_info_id for post
            $cleanUser['date_info_id'] = $driver->getLastGeneratedValue();

            // Create user
            $retVal = $this->getUserTable()->insert($user);

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
     * Checks if an email already exists for a user
     * @param string $email
     * @return boolean
     */
    public function checkEmailExistsInDb($email) {
        $rslt = $this->getUserContactRelTable()->select(
            array('email' => $email))->current();
        return !empty($rslt);
    }

    /**
     * Checks if screen name exists
     * @param string $screenName
     * @return boolean
     */
    public function checkScreenNameExistsInDb($screenName) {
        $rslt = $this->getUserContactRelTable()
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
     * Returns an encoded password
     * @param String $password
     * @return alnum md5 hash
     */
    public function encodePassword($password) {
        return $this->getHasher()->create_hash($password); //EDM_SALT . $password . EDM_PEPPER);
    }

    /**
     * Compares activation key to generated one.
     * @param string $key
     * @param array $user
     * @return boolean
     */
    public function isActivationKeyValid($key, $user) {
        return $key === $this->generateActivationKey(
            $user->firstName, $user->lastName, $user->email);
    }

    /**
     * Returns a unique screen name with length of "screen name length"
     * @param int $screenNameLength
     * @return string
     */
    public function generateUniqueScreenName($screenNameLength = 8) {
        $screenName = '';
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
     * Our password and activation key hasher.
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
                ->get('Edm\Db\TableGateway\UserContactRel');
        }
        return $this->userContactRelTable;
    }
    
}
