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
     * @return UserProto
     */
    public function testCreate ($userData) {

        // Get user service
        $userService = $this->userService();

        // Get user id
        $id = $userService->create(
            $this->userProtoFromNestedArray($userData));

        // Assert id returned
        $this->assertInternalType('int', $id);
        
        return $id;
    }

    /**
     * @depends testCreate
     * @param int $id
     * @return UserProto
     */
    public function testUpdate ($id) {
        
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
        $rslt = $service->update($userProto->user_id, $userProto, $unchangedData);

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

        // Return the updated user
        return $updatedUserProto;
    }

    /**
     * @depends testUpdate
     * @param UserProto $userProto
     */
    public function testDelete (UserProto $userProto) {
        // Get service
        $userService = $this->userService();

        // Delete user
        $rslt = $userService->delete($userProto);

        // Test return value
        $this->assertEquals(true, $rslt);
    }

    public function testClass () {
        $service = $this->userService();
        $this->assertInstanceOf('Edm\Service\UserService', $service);
        $this->assertInstanceOf('Edm\Service\AbstractCrudService', $service);
    }

    public function testGetSelect () {
        $this->assertInstanceOf('Zend\Db\Sql\Select', $this->userService()->getSelect());
    }

    public function testGetById () {
        $id = 1;
        $service = $this->userService();
        $proto = $service->getUserById($id);
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\UserProto', $proto);
    }

    public function testGetByScreenName () {

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

    /**
     * Requires fully qualified 'contact' portion of user data.
     * @see Edm\Db\ResultSet\Proto\ContactProto
     * @dataProvider truthyCreationProvider
     * @param $userData
     */
    public function testGenerateActivationKey ($userData) {
        // Get user service
        $userService = $this->userService();

        // Get contact information
        $contact = $userData['contact'];
        $user = $userData['user'];

        // Generate an activation key
        $key = $userService->generateActivationKey($user['screenName'], $contact['email']);

        // Assert expected result
        $this->assertEquals(true, strlen($key) === 32);
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
        self::$userService->delete($user);
    }

}
