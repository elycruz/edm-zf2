<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/28/2015
 * Time: 12:34 AM
 */

namespace EdmTest\Service;

use EdmTest\Bootstrap;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    public static $userService;

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

        // Assert id returned
        $this->assertInternalType('int', $id);

        // Get inserted row
        $insertedRow = $service->getById($id);

        // Assert inserted user row was inserted correctly
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\UserProto', $insertedRow);

        // Remove inserted row
        $service->delete($id);
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
        $proto = $service->getById($id);
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

    public function userService () {
        return self::$userService;
    }

}
