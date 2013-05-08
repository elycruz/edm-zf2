<?php

namespace EdmTest\Service;

use EdmTest\Bootstrap,
    Edm\Service\AbstractService;

/**
 * Description of UserServiceTest
 *
 * @author ElyDeLaCruz
 */
class UserServiceTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Edm\Service\UserService
     */
    protected $userService;
    protected $numUsers = 8;
    protected $data = array();
    protected $userIds = array();
    protected $userScreenNames = array();
    protected $userKeys = array(
        'screenName',
        'password',
    );
    protected $contactKeys = array(
        'firstName',
        'lastName',
        'email'
    );

    protected function setUp() {
        $this->serviceLocator = Bootstrap::getServiceManager();
        $this->userService = $this->serviceLocator
                ->get('Edm/Service/UserService');
        $this->generateData();
    }

    public function testNumUserData() {
        return $this->assertCount(8, $this->data);
    }

    public function testDbConnection() {
        $dbAdapter = $this->userService->getDb();
        $this->assertInstanceOf('Zend\Db\Adapter\Adapter', $dbAdapter);
        $schema = $dbAdapter->getDriver()->getConnection()->getCurrentSchema();
        $this->assertEquals('edm-0.4.0', $schema);
    }

    public function testCreateAndDeleteUsers() {
        // Create Users
        for ($i = 0; $i < $this->numUsers; $i += 1) {
            $user = $this->data[$i];
            $this->userIds[] = $this->userService->createUser($user);
            $this->userService->getById($this->userIds[$i]);
        }

        // Dump created users
        var_dump($this->userService->read(array(
            'fetchMode' => AbstractService::FETCH_RESULT_SET_TO_ARRAY  )));
        $this->assertCount(8, $this->userIds, 'user ids length check: Good!.');
    }

    public function testGetByScreenName() {
        $rslt = $this->userService->getByScreenName($this->data[0]['user']['screenName'], AbstractService::FETCH_FIRST_AS_ARRAY_OBJ);
        return $this->assertNotEmpty($rslt, 'User Screen Name: ' . $rslt->screenName);
    }

    protected function generateData() {
        for ($i = 0; $i < $this->numUsers; $i += 1) {
            $this->data[] = array(
                'contact' => $this->generateContactData($i),
                'user' => $this->generateUserData($i));
        }
    }

    protected function generateContactData($i) {
        $contact = array();
        foreach ($this->contactKeys as $key) {
            switch ($key) {
                case 'firstName' :
                    $value = 'First Name' . $i;
                    break;
                case 'lastName' :
                    $value = 'Last Name ' . $i;
                    break;
                case 'email' :
                    $value = 'email' . $i . '@domain.com';
                    break;
                default:
                    $value = '';
                    break;
            }
            if (preg_match('/_id$/', $key) > 0) {
                continue;
            }
            $contact[$key] = $value;
        }
        return $contact;
    }

    protected function generateUserData($i) {
        $user = array();
        foreach ($this->userKeys as $key) {
            switch ($key) {
                case 'screenName' :
                    $value = '';
                    break;
                case 'password' :
                    $value = 'password' . $i;
                    break;
                default:
                    $value = null;
                    break;
            }
            $user[$key] = $value;
        }
        return $user;
    }

    protected function tearDown() {
        // Delete users
        foreach ($this->userIds as $i) {
            $this->userService->deleteUser($i);
        }
    }

}
