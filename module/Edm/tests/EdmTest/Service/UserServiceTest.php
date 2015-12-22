<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/28/2015
 * Time: 12:34 AM
 */

namespace EdmTest\Service;

use EdmTest\Bootstrap,
    Edm\Db\ResultSet\Proto\UserProto;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    public static $userService;

    public static $qualifyingUserData = [
        // Only include required columns (others are defaulted in db)
        'user' => [
            'screenName' => 'SomeScreenName',
            'password' => 'helloworld',
            'activationKey' => 'helloworld'
        ],
        'contact' => [
            'email' => 'some@email.com',
            'altEmail' => 'some-alt@email.com',
            'name' => 'Some name',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'middleName' => 'Middle Name',
            'userParams' => ''
        ]
    ];

    /**
     * Ids that are created throughout tests that should not be left in database.
     * @var array
     */
    public static $userProtosToDelete = [];

    public static function setUpBeforeClass () {
        self::$userService = Bootstrap::getServiceManager()
            ->get('Edm\Service\UserService');
    }
    
    /**
     * @var Int
     */
    public static $createdUserId = null;

    public function truthyCreationProvider () {
        return [
            [[
                // Only include required columns (others are defaulted in db)
                'user' => [
                    'screenName' => 'SomeScreenName',
                    'password' => 'helloworld',
                    'activationKey' => 'helloworld'
                ],
                'contact' => [
                    'email' => 'some@email.com',
                    'altEmail' => 'some-alt@email.com',
                    'name' => 'Some name',
                    'firstName' => 'First Name',
                    'lastName' => 'Last Name',
                    'middleName' => 'Middle Name',
                    'userParams' => ''
                ]
            ]]
        ];
    }

    /**
     * @dataProvider truthyCreationProvider
     * @param array $userData
     */
    public function testCreateUser ($userData) {

        // Get user service
        $userService = $this->userService();

        // Get user id
        $id = $userService->createUser(
            $this->userProtoFromNestedArray($userData));

        // Assert id returned
        $this->assertInternalType('int', $id);

        self::$createdUserId = $id;
    }

    public function testRead () {
        // Set id to search for
        $id = 1;

        // Get service
        $service = $this->userService();

        // Get read result
        $rslt = $service->read(['where' => ['user_id' => $id]]);

        // Get row
        $proto = $rslt->current();

        // Assert correct result set type
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $rslt);

        // Assert only one item with current id
        $this->assertEquals(1, $rslt->count());

        // Assert correct proto class was returned by `read`
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\UserProto', $proto);
    }
    
    public function testUpdateUser () {
        $id = self::$createdUserId;
        
        // Get service
        $service = $this->userService();
        
        // Get user
        $userProto = $service->getUserById($id);

        // Clone user proto
        $unchangedData = $this->userProtoFromNestedArray($userProto->toNestedArray());

        // Get contact
        $contact = $userProto->getContactProto();

        // Update row
        $contact->firstName = 'Rice';
        $contact->lastName = 'Krispies';
        $contact->middleName = 'Bob';
        $userProto->role = 'guest';

        // Update row
        $rslt = $service->updateUser($userProto->user_id, $userProto, $unchangedData);

        // Assert user was updated successfully
        $this->assertEquals(true, $rslt);

        // Get updated row
        $updatedUserProto = $service->getUserById($userProto->user_id);
        $contact = $updatedUserProto->getContactProto();

        // Assert updates were made successfully
        $this->assertEquals('Rice', $contact->firstName);
        $this->assertEquals('Krispies', $contact->lastName);
        $this->assertEquals('Bob', $contact->middleName);
        $this->assertEquals('guest', $updatedUserProto->role);
        
        self::$createdUserId = $updatedUserProto->user_id;
    }

    public function testDeleteUser () {
        // Get service
        $userService = $this->userService();
        
        $userProto = $userService->getUserById(self::$createdUserId);

        // Delete user
        $rslt = $userService->deleteUser($userProto);

        // Test return value
        $this->assertEquals(true, $rslt);
    }

    public function testUserServiceClass () {
        $service = $this->userService();
        $this->assertInstanceOf('Edm\Service\UserService', $service);
        $this->assertInstanceOf('Edm\Service\AbstractCrudService', $service);
    }

    public function testGetSelect () {
        $this->assertInstanceOf('Zend\Db\Sql\Select', $this->userService()->getSelect());
    }

    public function testGetUserById () {
        $id = 1;
        $service = $this->userService();
        $proto = $service->getUserById($id);
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\UserProto', $proto);
    }

    /**
     * @dataProvider truthyCreationProvider
     * @param array $userData
     */
    public function testGetUserByScreenName ($userData) {
        $userService = $this->userService();
        $originalUserProto = $this->userProtoFromNestedArray($userData);
        $screenName = $originalUserProto->screenName;
        $user_id = $userService->createUser($originalUserProto);
        $proto = $userService->getUserByScreenName($screenName);
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\UserProto', $proto);
        $this->assertEquals($screenName, $proto->screenName);
        $this->assertEquals($user_id, $proto->user_id);
        $userService->deleteUser($proto);
    }
    
    /**
     * @dataProvider truthyCreationProvider
     * @param array $userData
     */
    public function testGetUserByEmail ($userData) {
        $userService = $this->userService();
        $originalUserProto = $this->userProtoFromNestedArray($userData);
        $email = $originalUserProto->getContactProto()->email;
        $userService->createUser($originalUserProto);
        $proto = $userService->getUserByEmail($email);
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\UserProto', $proto);
        $this->assertEquals($email, $proto->getContactProto()->email);
        $userService->deleteUser($proto);
    }

    /**
     * @dataProvider truthyCreationProvider
     * @param array $userData
     */
    public function testIsUserEmailInDb ($userData) {
        $userService = $this->userService();
        $originalUserProto = $this->userProtoFromNestedArray($userData);
        $email = $originalUserProto->getContactProto()->email;
        $user_id = $userService->createUser($originalUserProto);
        $userProto = $userService->getUserById($user_id);
        $result = $userService->isUserEmailInDb($email);
        $this->assertEquals(true, $result);
        $userService->deleteUser($userProto);
    }

    /**
     * @dataProvider truthyCreationProvider
     * @param array $userData
     */
    public function testIsUserScreenNameInDb ($userData) {
        $userService = $this->userService();
        $originalUserProto = $this->userProtoFromNestedArray($userData);
        $screenName = $originalUserProto->screenName;
        $user_id = $userService->createUser($originalUserProto);
        $userProto = $userService->getUserById($user_id);
        $result = $userService->isUserScreenNameInDb($screenName);
        $this->assertEquals(true, $result);
        $userService->deleteUser($userProto);
    }
    
    /**
     * @todo this test needs updating because Pbkdf2Hasher is going to become dynamic
     */
    public function testEncodeUserPassword () {
        $userService = $this->userService();
        $password = 'some-password-here';
        $encodedPassword = $userService->encodeUserPassword($password);
        $this->assertEquals(76, strlen($encodedPassword));
        
    }

    /**
     * Requires fully qualified 'contact' portion of user data.
     * @see Edm\Db\ResultSet\Proto\ContactProto
     * @dataProvider truthyCreationProvider
     * @param $userData
     */
    public function testGenerateUserActivationKey ($userData) {
        // Get user service
        $userService = $this->userService();

        // Get contact information
        $contact = $userData['contact'];
        $user = $userData['user'];
        
        // Generate an activation key
        $key = $userService->generateUserActivationKey($user['screenName'], $contact['email']);
        
        // Assert expected result
        $this->assertEquals(true, strlen($key) === 32);
    }
    
    /**
     * Requires fully qualified 'contact' portion of user data.
     * @see Edm\Db\ResultSet\Proto\ContactProto
     * @dataProvider truthyCreationProvider
     * @param $userData
     */
    public function testIsValidUserActivationKey ($userData) {
        // Get user service
        $userService = $this->userService();
        
        // Timestamp
        $timestamp = (new \DateTime())->getTimestamp();

        // Get contact information
        $email = $userData['contact']['email'];
        $screenName = $userData['user']['screenName'];
        
        // Generate an activation key
        $key = $userService->generateUserActivationKey($screenName, $email, $timestamp);
        
        $result = $userService->isValidUserActivationKey(
                $key, $screenName, $email, $timestamp);
        
        // Assert expected result
        $this->assertEquals(true, $result);
    }
    /**
     * @dataProvider truthyCreationProvider
     * @note The tests here are a bit Naive because in all actually it should 
     * run upto 10 to the power of 8 rows (since screenName's default length is 8 chars).
     * @todo See how long it takes to run this test on 10 to the power of 8 rows.
     * @param $userData
     */
    public function testGenerateUniqueUserScreenName ($userData) {
        $userService = $this->userService();
        $screenNameLen = 8;
        $numUsers = 3;
        $usersToDelete = [];
        $oldScreenName = '';
        for ($i = 0; $i <= $numUsers; $i += 1) {
            $userProto = $this->userProtoFromNestedArray($userData);
            $userProto->getContactProto()->email = ($i + 1) . $userProto->getContactProto()->email;
            $screenName = $userService->generateUniqueUserScreenName($screenNameLen);
            $userProto->screenName = $screenName;
            $user_id = $userService->createUser($userProto);
            $usersToDelete[] = $userService->getUserById($user_id);
            $this->assertNotEquals($oldScreenName, $screenName);
            $this->assertEquals($screenNameLen, strlen($screenName));
            $oldScreenName = $screenName;
        }
        foreach ($usersToDelete as $userProto) {
            $userService->deleteUser($userProto);
        }
    }
    
    public function testGenerateUuid () {
        $length = 8; // uuid length
        $userService = $this->userService();
        $uuid = $userService->generateUuid($length);
        $uuidMatchesRequiredPattern = preg_match('/^[a-z0-9A-Z]{' . $length . '}$/', $uuid);
        $this->assertEquals($length, strlen($uuid));
        $this->assertEquals(1, $uuidMatchesRequiredPattern);
    }
    
    public function testGetHasher () {
        $userService = $this->userService();
        $this->assertInstanceOf('\Edm\Hasher\Pbkdf2Hasher', $userService->getHasher());
    }
    
    public function testGetUserTable () {
        $userService = $this->userService();
        $this->assertInstanceOf('\Edm\Db\TableGateway\UserTable', $userService->getUserTable());
    }
    
    public function testGetContactTable () {
        $userService = $this->userService();
        $this->assertInstanceOf('\Edm\Db\TableGateway\ContactTable', $userService->getContactTable());
    }
    
    public function testGetUserContactRelTable () {
        $userService = $this->userService();
        $this->assertInstanceOf('\Edm\Db\TableGateway\ContactUserRelTable', $userService->getContactUserRelTable());
    }

    public function userService () {
        return self::$userService;
    }

    public function userProtoFromNestedArray ($nestedArray) {
        $proto = new UserProto();
        $proto->exchangeNestedArray($nestedArray);
        return $proto;
    }

    public static function tearDownAfterClass () {
        $userService = self::$userService;
        $user = $userService->getUserByScreenName('SomeScreenName');
        if ($user instanceof UserProto === false) {
            return;
        }
        self::$userService->deleteUser($user);
    }

}
