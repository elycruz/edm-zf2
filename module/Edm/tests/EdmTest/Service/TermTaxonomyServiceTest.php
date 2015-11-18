<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/17/2015
 * Time: 2:58 PM
 */

namespace EdmTest\Service;

use EdmTest\Bootstrap;

class TermTaxonomyServiceTest  extends \PHPUnit_Framework_TestCase {

    public static $termTaxService;

    public $requiredTermTaxProtoKeys = [
        'term_taxonomy_id',
        'term_alias',
        'taxonomy',
        'description',
        'childCount',
        'assocItemCount',
        'listOrder',
        'parent_id'
    ];

    public static function setUpBeforeClass() {
        $locator = Bootstrap::getServiceManager();
        self::$termTaxService = $locator->get('Edm\\Service\\TermTaxonomyService');
    }

    public function testExistence () {
        $this->assertInstanceOf('Edm\\Service\\TermTaxonomyService', self::$termTaxService);
    }

    public function termTaxService () {
        return self::$termTaxService;
    }

    public function testGetById () {
        $rslt = $this->termTaxService()->getById(1);
        $this->assertCorrectProtoClass($rslt);
        $this->assertEquals(1, $rslt->term_taxonomy_id);
        $this->assertProtoContainsRequiredKeys($rslt, $this->requiredTermTaxProtoKeys);
    }

    public function testGetByAlias () {
        $termTaxService = $this->termTaxService();
        $rslt = $termTaxService->getByAlias('user-group', 'taxonomy');
        $this->assertCorrectProtoClass($rslt);
        $this->assertProtoContainsRequiredKeys($rslt, $this->requiredTermTaxProtoKeys);
        $this->assertEquals('user-group', $rslt->term_alias);
        $this->assertEquals('taxonomy', $rslt->taxonomy);
    }

    protected function assertProtoContainsRequiredKeys ($proto, $requiredKeys) {
        foreach ($requiredKeys as $key) {
            $this->assertEquals(true, isset($proto->{$key}),
                'Instance of fetched `TermTaxonomyProto` ' .
                'should have a set "' + $key + '" property key."');
        }
    }

    protected function assertCorrectProtoClass ($proto) {
        $this->assertInstanceOf('\\Edm\Db\ResultSet\\Proto\\TermTaxonomyProto',
            $proto);
    }
}

