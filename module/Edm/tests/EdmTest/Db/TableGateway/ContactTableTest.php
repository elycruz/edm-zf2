<?php

namespace EdmTest\Db\TableGateway;

use Edm\Db\TableGateway\ContactTable;

class ContactTableTest extends \PHPUnit_Framework_TestCase {
    public static $contactTable;
    
    public static function setUpBeforeClass () {
        self::$contactTable = new ContactTable();
    }

    public function testAlias () {
       $this->assertEquals('contact', $this->contactTable()->alias);
    }

    public function testTableName () {
        $this->assertEquals('contacts', $this->contactTable()->table);
    }

    public function testModelClass () {
        $this->assertEquals('Edm\Db\ResultSet\Proto\ContactProto',
            $this->contactTable()->modelClass);
    }

    public function contactTable () {
        return self::$contactTable;
    }
}

