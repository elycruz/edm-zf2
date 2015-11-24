<?php

namespace EdmTest\Db\TableGateway;

use Edm\Db\TableGateway\DateInfoTable;

class DateInfoTableTest extends \PHPUnit_Framework_TestCase {
    public static $dateInfoTable;
    
    public static function setUpBeforeClass () {
        self::$dateInfoTable = new DateInfoTable();
    }

    public function testAlias () {
       $this->assertEquals('dateInfo', $this->dateInfoTable()->alias);
    }

    public function testTableName () {
        $this->assertEquals('date_info', $this->dateInfoTable()->table);
    }

    public function testModelClass () {
        $this->assertEquals('Edm\Db\ResultSet\Proto\DateInfoProto',
            $this->dateInfoTable()->modelClass);
    }

    public function dateInfoTable () {
        return self::$dateInfoTable;
    }
}

