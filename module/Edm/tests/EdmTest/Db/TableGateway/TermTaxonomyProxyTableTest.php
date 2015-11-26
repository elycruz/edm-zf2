<?php

namespace EdmTest\Db\TableGateway;

use Edm\Db\TableGateway\TermTaxonomyProxyTable,
    Edm\Db\ResultSet\Proto\TermTaxonomyProxyProto;

class TermTaxonomyProxyTableTest extends \PHPUnit_Framework_TestCase {
    public static $termTaxonomyProxyTable;
    
    public static function setUpBeforeClass () {
        self::$termTaxonomyProxyTable = new TermTaxonomyProxyTable();
    }

    public function testAlias () {
       $this->assertEquals('termTaxonomyProxy', $this->termTaxonomyProxyTable()->alias);
    }

    public function testTableName () {
        $this->assertEquals('term_taxonomies_proxy', $this->termTaxonomyProxyTable()->table);
    }

    public function testAliasAndProtoFormKey () {
        $proto = new TermTaxonomyProxyProto();
        $this->assertEquals($proto->getFormKey(), $this->termTaxonomyProxyTable()->alias);
    }
    
    public function testModelClass () {
        $this->assertEquals('Edm\Db\ResultSet\Proto\TermTaxonomyProxyProto',
            $this->termTaxonomyProxyTable()->modelClass);
    }

    public function termTaxonomyProxyTable () {
        return self::$termTaxonomyProxyTable;
    }
}

