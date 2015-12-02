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

    /**
     * Ids that are created throughout tests that should not be left in database.
     * @var array
     */
    public static $userIdsToDelete = [];

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
     */
    public function testCreate ($userData) {
        // Get service
        $service = $this->userService();

        // Create user
        $id = $service->create($userData);
        self::$userIdsToDelete[] = $id;

        // Assert id returned
        $this->assertInternalType('int', $id);

        // Get inserted row
        $insertedRow = $service->getUserById($id);

        // Assert inserted user row was inserted correctly
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\UserProto', $insertedRow);

        // Remove created row
        $service->delete($id);

        // Return row for next test
        return $insertedRow;
    }

    /**
     * @dataProvider truthyCreationProvider
     * @param array $userData
     */
    public function testUpdate ($userData) {
        // Get service
        $service = $this->userService();

        // Create user
        $id = $service->create($userData);
        self::$userIdsToDelete[] = $id;

        // Get created user
        $userProto = $service->getUserById($id);

        // Get contact
        $contact = $userProto->getContactProto();

        // Update row
        $contact->firstName = 'Rice';
        $contact->lastName = 'Krispies';
        $contact->middleName = 'Bob';
        $userProto->role = 'guest';

        // Update row
        $service->update($userProto->user_id,
            $contact->email,
            $userProto->toArrayNested(UserProto::FOR_OPERATION_DB_UPDATE));

        // Get updated row
        $updatedUserProto = $service->getUserById($userProto->user_id);
        $contact = $updatedUserProto->getContactProto();

        // Assert updates were made successfully
        $this->assertEquals('Rice', $contact->firstName);
        $this->assertEquals('Krispies', $contact->lastName);
        $this->assertEquals('Bob', $contact->middleName);
        $this->assertEquals('guest', $updatedUserProto->role);

        // Delete created user
        $service->delete($userProto->user_id);

        // Return updated row for deletion
        return $updatedUserProto;
    }

    /**
     * @dataProvider truthyCreationProvider
     * @param array $userData
     */
    public function testDelete ($userData) {
        // Get service
        $userService = $this->userService();

        // Create user
        $id = $userService->create($userData);
        self::$userIdsToDelete[] = $id;

        // Get user
        $userProto = $userService->getUserById($id);

        // Delete user
        $rslt = $userService->delete($userProto->user_id);

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

    public static function tearDownAfterClass () {
        $userService = self::$userService;
        foreach(self::$userIdsToDelete as $user_id) {
            $userService->delete($user_id);
        }
    }

}
