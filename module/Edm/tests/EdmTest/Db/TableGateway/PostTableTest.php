<?php

namespace EdmTest\Db\TableGateway;

use Edm\Db\TableGateway\PostTable;

class PostTableTest extends \PHPUnit_Framework_TestCase {
    public static $postTable;
    
    public static function setUpBeforeClass () {
        self::$postTable = new PostTable();
    }

    public function testAlias () {
       $this->assertEquals('post', $this->postTable()->alias);
    }

    public function testTableName () {
        $this->assertEquals('posts', $this->postTable()->table);
    }

    public function testModelClass () {
        $this->assertEquals('Edm\Db\ResultSet\Proto\PostProto',
            $this->postTable()->modelClass);
    }

    public function postTable () {
        return self::$postTable;
    }
}

