<?php

namespace EdmTest\Db\TableGateway;

use Edm\Db\TableGateway\UserTable;

class UserTableTest extends \PHPUnit_Framework_TestCase {
    public static $userTable;
    
    public static function setUpBeforeClass () {
        self::$userTable = new UserTable();
    }

    public function testAlias () {
       $this->assertEquals('user', $this->userTable()->alias);
    }

    public function testTableName () {
        $this->assertEquals('users', $this->userTable()->table);
    }

    public function testModelClass () {
        $this->assertEquals('Edm\Db\ResultSet\Proto\UserProto',
            $this->userTable()->modelClass);
    }

    public function userTable () {
        return self::$userTable;
    }
}

