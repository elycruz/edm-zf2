<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/17/2015
 * Time: 2:58 PM
 * @todo Allow Crud Services to use the RowGatewayPattern (@see TermTaxonomyService).
 */

namespace EdmTest\Service;

use EdmTest\Bootstrap,
    Edm\Db\ResultSet\Proto\TermProto,
    Edm\Db\ResultSet\Proto\TermTaxonomyProto;

class TermTaxonomyServiceTest  extends \PHPUnit_Framework_TestCase {

    public static $termTaxService;

    public static $idsToCleanup = [];

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

    public $requiredTaxonomyAliasForEdm = [
        'taxonomy',
        'user-group',
        'address-type',
        'comment-status',
        'commenting-status',
        'contact-type',
        'extender-table',
        'page-type',
        'helper-type',
        'not-allowed-as-extender-table',
        'phone-type',
        'post-category',
        'post-status',
        'post-type',
        'security-question',
        'tag',
        'ui-position',
        'uncategorized',
        'user-status',
        'view-module-type',
        'mixed-term-type'
    ];

    public static function setUpBeforeClass() {
        $locator = Bootstrap::getServiceManager();
        self::$termTaxService = $locator->get('Edm\\Service\\TermTaxonomyService');
    }

    public function testExistence () {
        $this->assertInstanceOf('Edm\\Service\\TermTaxonomyService', self::$termTaxService);
    }

    /**
     * @return \Edm\Service\TermTaxonomyService
     */
    public function termTaxService () {
        return self::$termTaxService;
    }

    public function testGetById () {
        $rslt = $this->termTaxService()->getById(1);
        $this->assertCorrectProtoClass($rslt);
        $this->assertEquals(1, $rslt->term_taxonomy_id);
        $this->assertProtoContainsRequiredKeys($rslt);
    }

    public function testGetByAlias () {
        $termTaxService = $this->termTaxService();
        $rslt = $termTaxService->getByAlias('user-group', 'taxonomy');
        $this->assertCorrectProtoClass($rslt);
        $this->assertProtoContainsRequiredKeys($rslt);
        $this->assertEquals('user-group', $rslt->term_alias);
        $this->assertEquals('taxonomy', $rslt->taxonomy);
    }

    public function testGetByTaxonomy () {
        $termTaxService = $this->termTaxService();
        $rslt = $termTaxService->getByTaxonomy('taxonomy');
        $item0 = $rslt->current();

        // Check first item in result set for correct proto
        $this->assertCorrectProtoClass($item0);
        $this->assertProtoContainsRequiredKeys($item0);

        // Asset correct result set class
        $this->assertCorrectResultSetClass($rslt);

        // Assert fetched rows are indeed "taxonomy's"
        foreach ($rslt as $row) {
            $this->assertContains($row->term_alias, $this->requiredTaxonomyAliasForEdm);
        }
    }

    public function testSetListOrderById () {
        $termTaxService = $this->termTaxService();

        // Fetch
        $rslt = $termTaxService->getById(1);

        // Store
        $oldListOrder = $rslt->listOrder;

        // Update
        $termTaxService->setListOrderById(1, 1000);

        // Test
        $rslt = $termTaxService->getById(1);
        $this->assertEquals(1000, $rslt->listOrder);

        // Revert
        $termTaxService->setListOrderById(1, $oldListOrder);
        $rslt = $termTaxService->getById(1);
        $this->assertEquals($oldListOrder, $rslt->listOrder);
    }

    public function testGetSelect () {
        $select = $this->termTaxService()->getSelect();
        $this->assertInstanceOf('Zend\Db\Sql\Select', $select);
    }

    public function testCreate () {
        $data = [
            'term-taxonomy' => [
                'term_alias' => 'some-term-taxonomy-here',
                'taxonomy' => 'uncategorized',
                'description' => '',
                'accessGroup' => 'cms-manager'
            ],
            'term' => [
                'name' => 'Some Term Taxonomy Here',
                'alias' => 'some-term-taxonomy-here',
                'term_group_alias' => 'edm-term-taxonomy-service-test'
            ]];

        // Create test term taxonomy
        $retVal = $this->termTaxService()->create($data);

        // Assert an 'id' was returned from `create` process
        $this->assertEquals(true, is_numeric($retVal));

        self::$idsToCleanup[] = $retVal;

        // Return result of creation process
        return $retVal;
    }

    /**
     * @depends testCreate
     * @param int $id
     * @return int
     */
    public function testUpdate ($id) {
        // Get data for update
        $newData = [
            'term-taxonomy' => [
                'description' => 'Some description here',
                'accessGroup' => 'user'
            ],
            'term' => [
                'name' => 'Some Term Taxonomy Hereio bob'
            ]];

        // Get term taxonomy service
        $termTaxService = $this->termTaxService();

        $originalRslt = $termTaxService->getById($id);
        $data = array_merge($originalRslt->toArray(TermTaxonomyProto::FOR_OPERATION_FORM), $newData);

//        var_dump($data);

        // Create test term taxonomy
        $retVal = $termTaxService->update($id, $data);

        // Assert an 'id' was returned from `create` process
        $this->assertEquals(true, is_numeric($retVal));

        // Get updated row
        $rslt = $termTaxService->getById($id);

        // Assert that updates were made
        $this->assertEquals($data['term-taxonomy']['description'], $rslt->description);
        $this->assertEquals($data['term-taxonomy']['accessGroup'], $rslt->accessGroup);
        $this->assertEquals($data['term']['name'], $rslt->term->name);

        // Return result of creation process
        return $retVal;
    }

    /**
     * @depends testUpdate
     * @param int $id
     * @throws \Exception
     */
    public function testDelete ($id) {
        $this->assertEquals(true, is_numeric($this->termTaxService()->delete($id)));
    }

    public function testGetTermTaxonomyTable () {
        $this->assertInstanceOf('Edm\\Db\\TableGateway\\TermTaxonomyTable',
            $this->termTaxService()->getTermTaxonomyTable());
    }

    public function testGetTermTaxonomyProxyTable () {
        $this->assertInstanceOf('Edm\\Db\\TableGateway\\TermTaxonomyProxyTable',
            $this->termTaxService()->getTermTaxonomyProxyTable());
    }

    public function testGetTermTable () {
        $this->assertInstanceOf('Edm\\Db\\TableGateway\\TermTable',
            $this->termTaxService()->getTermTable());
    }

    protected function assertProtoContainsRequiredKeys ($proto) {
        $requiredKeys = $this->requiredTermTaxProtoKeys;
        foreach ($requiredKeys as $key) {
            $this->assertEquals(true, isset($proto->$key),
                'Instance of fetched `TermTaxonomyProto` ' .
                'should have a set "' + $key + '" property key."');
        }
    }

    protected function assertCorrectProtoClass ($proto) {
        $this->assertInstanceOf('\\Edm\Db\ResultSet\\Proto\\TermTaxonomyProto', $proto);
    }

    protected function assertCorrectResultSetClass ($rsltSet) {
        $this->assertInstanceOf('\\Zend\\Db\\ResultSet\\ResultSet', $rsltSet);
    }

    public static function tearDownAfterClass () {
        foreach (self::$idsToCleanup as $id) { 
            self::$termTaxService->delete($id);
        }
    }

}
