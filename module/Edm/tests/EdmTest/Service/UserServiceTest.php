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

    public function userService () {
        return self::$userService;
    }

}
