<?php
/**
 * @author ElyDeLaCruz
 */
class Edm_Service_Internal_UserService 
extends Edm_Service_Internal_AbstractCrudService  {

    protected $_userModel;
    protected $_contactModel;
    protected $_userTermRelModel;
    protected $_secondaryModelMergeColumn = 'user_id';
    protected $_screenNameLength = 8;
    
    /**
     * Secondary Model
     * @var Edm_Db_AbstractTable
     */
    protected $_secondaryModel;

    /**
     * Constructs our service using its parent constructor
     */
    public function __construct() {
        $this->_userModel = Edm_Db_Table_ModelBroker::getModel('user');
        $this->_contactModel = Edm_Db_Table_ModelBroker::getModel('contact');
        $this->_userTermRelModel = Edm_Db_Table_ModelBroker::getModel('user-term-rel');
    }

    /**
     * Creates a User
     * @param array $data
     * @return mixed
     */
    public function createUser(array $data) {
        // Make sure we have our user data namespace
        if (!array_key_exists('user', $data) ||
            !array_key_exists('contact', $data)) {
            throw new Exception('A key is missing from the ' .
                'data array passed into the create user function of the ' .
                'front end user service.');
        }

        //--------------------------------------------------------------
        // Update user data
        //--------------------------------------------------------------
        $userData = $data['user'];
        $contactData = $data['contact'];

        // If user status not 'activated' then generate activation key
        $activationKey = $userData['activationKey'];
        if ($userData['status'] == 'pending-activation'
                && empty($userData['activationKey'])) {
            $activationKey = $this->generateActivationKey(
             $userData['firstName'], $userData['lastName'], $userData['email']);
        } 

        // Encrypt Password
        $password = $this->encodePassword($userData['password']);

        //--------------------------------------------------------------
        // Update user data
        //--------------------------------------------------------------
        $userData['password'] = $password;
        $userData['activationKey'] = $activationKey;
        $userData['registeredDate'] = Zend_Date::now()->getTimestamp();
        $userData['role'] = !empty($userData['role']) ? 
            $userData['role'] : 'user';

        // Get screen name if empty
        if (!array_key_exists('screenName', $userData) 
                || empty($userData['screenName'])) {
            
            $userData['screenName'] = $this
                    ->getAvailableScreenNameForEmail($contactData['email']);
        }
        
        //--------------------------------------------------------------
        // Update Contact
        //--------------------------------------------------------------
        if (empty($contactData['userParams'])) {
            $contactData['userParams'] = '';
        }

        //--------------------------------------------------------------
        // Check if this module has a secondary table or table that extends it
        //--------------------------------------------------------------
        if (array_key_exists('secondary', $data)) {
            $secondaryData = $data['secondary'];
            $secondaryModel = 
                Edm_Db_Table_ModelBroker::getModel(
                        $secondaryData['modelAlias']);
            $secondaryData = $secondaryData['data'];
        }
        
        //--------------------------------------------------------------
        // Get models
        //--------------------------------------------------------------
        $userModel = $this->_userModel;
        $userTermRelModel = $this->_userTermRelModel;

        //--------------------------------------------------------------
        // Begin db transaction
        //--------------------------------------------------------------
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            // Create Contact
            $contact_id = $this->_contactModel
                    ->createContact($contactData);

            // Update User data
            $userData['contact_id'] = $contact_id;

            // Create User
            $user_id = $userModel->createUser($userData);
            
            // Secondary model
            if (!empty($secondaryModel)) {
                $secondaryData['user_id'] = $user_id;
                $secondaryModel->insert($secondaryData);
            }

            $userTermRelModel
                ->createUserTermRel(array(
                    'email' => $contactData['email'], 
                    'screenName' => $userData['screenName']));

            // Success, commit to db
            $db->commit();

            // Return true to the user
            return true;
        } 
        catch (Exception $e) {
            $db->rollBack();
            return $e;
        }
    }

    /**
     * Gets a User by id
     * @param int $id
     * @return array
     */
    public function getUserById($id, $fetchMode = Zend_Db::FETCH_OBJ) {
        return $this->getSelect()->where('user.user_id=?', $id)
                ->query($fetchMode)->fetch();
    }
    
    /**
     * Gets a User by email
     * @param int $email
     * @return array
 */
    public function getUserByEmail($email, $fetchMode = Zend_Db::FETCH_OBJ) {
        return $this->getSelect()->where('contact.email=?', $email)
                ->query($fetchMode)->fetch();
    }

    /**
     *
     * @param string $email
     * @return boolean 
     */
    public function checkUserEmailExists($email) {
        $rslt = $this->_userTermRelModel->select()
                ->where('email=?', $email)->query()->fetchAll();
//        Zend_Debug::dump($rslt); exit();
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
        $rslt = $this->_userTermRelModel->select()
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
     *
     * @param type $seed
     * @return type 
     */
    public function getAvailableScreenNames($seed) {
        return array();
    }
    
    /**
     * Returns the select statement which joins the term_relationships data
     * and the user data for this `user_id` $id together
     * @return Zend_Db_Select
     */
    public function getSelect() {
        $select = $this->getDb()->select()
                ->from(array('user' => $this->_userModel->getName()), '*')
                ->join(array('contact' => $this->_contactModel->getName()),
                        'contact.contact_id = user.contact_id',
                        array('name' => 'CONCAT( firstName," ",lastName )',
                            '*'));
        
        // If secondary model
        if (!empty($this->_secondaryModel)) {
            $select->join(array(
                'secondary' => $this->_secondaryModel->getName()),
                    'secondary.' . $this->_secondaryModelMergeColumn .
                    '= user.' . $this->_secondaryModelMergeColumn);
        }       
                
        return $select->join(array('termTax' => 'term_taxonomies'), 
                    'termTax.term_alias = user.role',
                    array('term_alias'))
            ->join(array('term' => 'terms'), 
                    'term.alias = termTax.term_alias',
                    array('term_name' => 'name'));
    }

    /**
     * Update a User
     * @param int $id
     * @param array data
     * @return Boolean
     */
    public function updateUser($id, array $data) {
        // Make sure a sure a user with a user_id of $id exists
        // Get User for Ids (should be validated for existence
        // from controller level.
        $user = $this->getUserById($id);

        // Make sure we have our user data namespace
        if (!array_key_exists('user', $data)) {
            throw new Exception('A key is missing from the ' .
                'data array passed into the update user function of the ' .
                'front end user service.');
        }
        
        //--------------------------------------------------------------
        // Update user data
        //--------------------------------------------------------------
        $userData = $data['user'];

        if (array_key_exists('contact', $data) && !empty($data['contact'])) {
            $contactData = $data['contact'];
        }

        // Encrypt Password
        if (!empty($userData['password'])) {
            $userData['password'] =
                    $this->encodePassword($userData['password']);
        }

        // Today's date
        $today = Zend_Date::now();
        
        // Update user data
        $userData['lastUpdated'] = $today->getTimestamp();

        $auth = $this->getAuthAdapter();
        if ($auth->hasIdentity()) {
            $userData['lastUpdatedById'] = $auth->getIdentity()->user_id;
            
        }
        
        //--------------------------------------------------------------
        // Begin db transaction
        //--------------------------------------------------------------
        $this->_db->beginTransaction();
        try {
            
            if (!empty($contactData)) {
                // Update Contact
                $this->_contactModel
                        ->updateContact($user->contact_id, $contactData);
            }

            // Create User
            $this->_userModel->updateUser($user->user_id, $userData);

            // Success, commit to db
            $this->_db->commit();

            // Return true to the user
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            return $e;
        }
    }

    /**
     * Deletes a User
     * @param int $id
     * @return Boolean
     */
    public function deleteUser($id) {
        // Get user to delete
        $userToDelete = $this->getUserById($id);
        
        // If user doesn't exist bail
        if (empty($userToDelete)) {
            return false;
        }
        
        // Delete user
        // Delete user term rel
        // Delete contact 
        // Delete 
        
    }

    /**
     * Logs a user in and validates him/her by email and password
     * @param string $email
     * @param string $password
     */
    public function loginUser($email, $password) {
        // Encode password
        $password = $this->encodePassword($password);
        
        $auth = $this->getAuthAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable(
                        $this->getDb(), 
                        'user_lookup', 'email', 'password');
        $authAdapter->setIdentity($email);
        $authAdapter->setCredential($password);
        $authAdapter->getDbSelect()->where('status=?', 'activated');
        $rslt = $auth->authenticate($authAdapter);

        if ($rslt->isValid()) {
            // store the username, first and last names of the user
            $storage = $auth->getStorage();
            $storage->write($authAdapter->getResultRowObject(
                            array(
                                'user_id', 'email', 'lastLogin',
                                'role', 'registeredDate')));

            // Update user lastLogin
            $this->_userModel
                    ->updateLastLoginForId($auth->getIdentity()->user_id);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Clears the user token
     */
    public function logoutUser() {
        $auth = $this->getAuthAdapter();
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
            return true;
        }
        return false;
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
     * Activates user (sets status flag to activate)
     * @param int $id
     * @return boolean
     */
    public function activateUser($id) {
        return $this->_userModel->update(array('status' => 'activated'), 
                $this->_userModel->getWhereClauseFor($id, 'user_id'));
    }
    
    /**
     * Returns the users ip address
     * @return string
     */
    public function getUserIp() {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        } else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (!empty($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } else if (!empty($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } else if (!empty($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } else {
            return $_SERVER["REMOTE_ADDR"];
        }
    }
    
    /**
     * Returns the currently logged in user
     * @return mixed array|boolean 
     */
    public function getUser() {
        // If user has been fetched already return the fetched user
        if (!empty($this->_user)) {
            return $this->_user;
        }
        
        // Else get the user or return false
        $auth = $this->getAuthAdapter();
        if ($auth->hasIdentity()) {
            $user =                    
                $this->getUserById(
                    $auth->getIdentity()->user_id, 
                        Zend_Db::FETCH_ASSOC);

            if (!empty($user['userParams'])) {
                $userParams = 
                    $this->unSerializeAndUnEscapeMetadata($user['userParams']);
                $user['userParams'] = '';
                $user = $this->getDbDataHelper()->reverseEscapeTupleFromDb($user);
                $user['userParams'] = $userParams;
            }
            
            $this->_user = $user;
            return $user;
        }
        return false;
    }
    
    /**
     * Temporarily block user.  Cases:
     *  - Login attempt exceeds number of consecutive tries
     * @return boolean 
     */
    public function blockCurrUserTemp() {
        return false;
    }
    
    /**
     * Blocks user till further notice.  Use cases:
     *  - Abusive behaviour from user
     *  - User tries to hack forms or site repeatedly
     * @return boolean 
     */
    public function blockCurrUserPerm() {
        return false;
    }
    
    /**
     * Redirects user to some other server (preferably a counter strike server)
     * Use cases:
     *  - Denial of Service attack
     * @return boolean
     */
    public function blockCurrUserCold() {
        return false;
    }
    
    /**
     * Unblocks a user
     * @param type $id
     * @return boolean 
     */
    public function unblockUser($id) {
        return $id;
    }
    
    public function setSecondaryModel(Edm_Db_AbstractTable $model) {
        $this->_secondaryModel = $model;
    }
    
    public function getSecondaryModel() {
        return $this->_secondaryModel;
    }
    
    /**
     * Sets the column to merge the secondary model on
     * @param string $string 
     */
    public function setSecondaryModelMergeColumn($string) {
        $this->_secondaryModelMergeColumn = $string;
    }
    
    /**
     * Converts the user's ($user) user params variable to an unserialized 
     * reverse escaped array.
     * @param string $metaData
     * @return array $user
     */
    public function unSerializeAndUnEscapeMetadata($metaData) {
        return $this->getDbDataHelper()->reverseEscapeTupleFromDb(
                    unserialize($metaData));
    }
    
    /**
     * Serializes and escapes a metadata array to string
     * @param array $metaData
     * @return string 
     */
    public function escapeAndSerializeMetadata(array $metaData) {
        return serialize($this->getDbDataHelper()->escapeTupleForDb($metaData));
    }
    
    public function getAvailableScreenNameForEmail($email) {
        $screenName = $this->gen_uuid($this->_screenNameLength, $email);
        while ($this->checkScreenNameExists($screenName)) {
            $screenName = $this->gen_uuid($this->_screenNameLength, $email);
        }
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