<?php

namespace EdmTest\Db\TableGateway;

use Edm\Db\TableGateway\TermTaxonomyTable;

class TermTaxonomyTableTest extends \PHPUnit_Framework_TestCase {
    public static $termTaxonomyTable;
    
    public static function setUpBeforeClass () {
        self::$termTaxonomyTable = new TermTaxonomyTable();
    }

    public function testAlias () {
       $this->assertEquals('termTaxonomy', $this->termTaxonomyTable()->alias);
    }

    public function testTableName () {
        $this->assertEquals('term_taxonomies', $this->termTaxonomyTable()->table);
    }

    public function testModelClass () {
        $this->assertEquals('Edm\Db\ResultSet\Proto\TermTaxonomyProto',
            $this->termTaxonomyTable()->modelClass);
    }

    public function termTaxonomyTable () {
        return self::$termTaxonomyTable;
    }
}

