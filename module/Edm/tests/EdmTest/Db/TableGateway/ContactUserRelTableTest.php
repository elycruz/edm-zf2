<?php

namespace EdmTest\Db\TableGateway;

use Edm\Db\TableGateway\ContactUserRelTable;

class ContactUserRelTableTest extends \PHPUnit_Framework_TestCase {
    public static $contactUserRelTable;
    
    public static function setUpBeforeClass () {
        self::$contactUserRelTable = new ContactUserRelTable();
    }

    public function testAlias () {
       $this->assertEquals('contactUserRel', $this->contactUserRelTable()->alias);
    }

    public function testTableName () {
        $this->assertEquals('user_contacts_relationships', $this->contactUserRelTable()->table);
    }

    public function contactUserRelTable () {
        return self::$contactUserRelTable;
    }
}

