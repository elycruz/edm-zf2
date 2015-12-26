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
    Edm\Db\ResultSet\Proto\TermTaxonomyProto;

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
        $rslt = $this->termTaxService()->getTermTaxonomyById(1);
        $this->assertCorrectProtoClass($rslt);
        $id = (int) $rslt->term_taxonomy_id;
        $this->assertEquals(1, $id);
        $this->assertProtoContainsRequiredKeys($rslt);
    }

    public function testGetByAlias () {
        $termTaxService = $this->termTaxService();
        $rslt = $termTaxService->getTermTaxonomyByAlias('user-group', 'taxonomy');
        $this->assertCorrectProtoClass($rslt);
        $this->assertProtoContainsRequiredKeys($rslt);
        $this->assertEquals('user-group', $rslt->term_alias);
        $this->assertEquals('taxonomy', $rslt->taxonomy);
    }

    public function testGetByTaxonomy () {
        $termTaxService = $this->termTaxService();
        $rslt = $termTaxService->getTermTaxonomyByTaxonomy('taxonomy');
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

    public function testSetListOrderForTermTaxonomy () {
        $termTaxService = $this->termTaxService();

        // Fetch
        $termTaxonomy = $termTaxService->getTermTaxonomyById(1);

        // Store
        $termTaxonomy->storeSnapshot();
        $oldListOrder = $termTaxonomy->getStoredSnapshotValues()['listOrder'];
        $termTaxonomy->listOrder = 1000;

        // Update
        $termTaxService->setListOrderForTaxonomy($termTaxonomy);

        // Test
        $fetchedTermTaxonomy = $termTaxService->getTermTaxonomyById(1);
        $this->assertEquals(1000, $fetchedTermTaxonomy->listOrder);

        // Revert
        $termTaxonomy->listOrder = $oldListOrder;
        $termTaxService->setListOrderForTaxonomy($termTaxonomy);
        $fetchedTermTaxonomy1 = $termTaxService->getTermTaxonomyById(1);
        $this->assertEquals($oldListOrder, $fetchedTermTaxonomy1->listOrder);
    }

    public function testGetSelect () {
        $select = $this->termTaxService()->getSelect();
        $this->assertInstanceOf('Zend\Db\Sql\Select', $select);
    }

    public function testCreate () {
        $data = [
            'termTaxonomy' => [
                'term_alias' => 'some-termTaxonomy-here',
                'taxonomy' => 'uncategorized',
                'description' => '',
                'accessGroup' => 'cms-manager'
            ],
            'term' => [
                'name' => 'Some Term Taxonomy Here',
                'alias' => 'some-termTaxonomy-here',
                'term_group_alias' => 'edm-termTaxonomy-service-test'
            ]];

        // Create test term taxonomy
        $retVal = $this->termTaxService()->create($data);

        // Delete inserted row
        $this->termTaxService()->delete($retVal);

        // Assert an 'id' was returned from `create` process
        $this->assertEquals(true, is_numeric($retVal), $retVal);

        // Return result of creation process
        return $retVal;
    }

    /**
     * @return int
     */
    public function testUpdate () {
        $data = [
            'termTaxonomy' => [
                'term_alias' => 'some-termTaxonomy-here',
                'taxonomy' => 'uncategorized',
                'description' => '',
                'accessGroup' => 'cms-manager'
            ],
            'term' => [
                'name' => 'Some Term Taxonomy Here',
                'alias' => 'some-termTaxonomy-here',
                'term_group_alias' => 'edm-termTaxonomy-service-test'
            ]];

        // Get term taxonomy service
        $termTaxService = $this->termTaxService();

        // Create test term taxonomy
        $id = $termTaxService->create($data);

        $originalRslt = $termTaxService->getTermTaxonomyById($id);
        $originalRslt->description = 'Some description here.';
        $originalRslt->accessGroup = 'user';
        $originalRslt->getTermProto()->name = 'Some Term Taxonomy Hereio Bob';
        $data = $originalRslt->toNestedArray(TermTaxonomyProto::FOR_OPERATION_DB_UPDATE);

        // Create test term taxonomy
        $retVal = $termTaxService->update($id, $data);

        // Get updated row
        $rslt = $termTaxService->getTermTaxonomyById($id);

        // Delete inserted row
        $this->termTaxService()->delete($id);

        // Assert an 'id' was returned from `create` process
        $this->assertNotInstanceOf('\Exception', $retVal, $retVal);

        // Assert that updates were made
        $this->assertEquals($data['termTaxonomy']['description'], $rslt->description);
        $this->assertEquals($data['termTaxonomy']['accessGroup'], $rslt->accessGroup);
        $this->assertEquals($data['term']['name'], $rslt->getTermProto()->name);
        $this->assertEquals($data['term']['term_group_alias'], $rslt->getTermProto()->term_group_alias);

        // Return result of creation process
        return $retVal;
    }

    public function testDelete () {
        $data = [
            'termTaxonomy' => [
                'term_alias' => 'some-termTaxonomy-here',
                'taxonomy' => 'uncategorized',
                'description' => '',
                'accessGroup' => 'cms-manager'
            ],
            'term' => [
                'name' => 'Some Term Taxonomy Here',
                'alias' => 'some-termTaxonomy-here',
                'term_group_alias' => 'edm-termTaxonomy-service-test'
            ]];

        // Get term taxonomy service
        $termTaxService = $this->termTaxService();

        // Create test term taxonomy
        $id = $termTaxService->create($data);
        $this->assertEquals(true, is_numeric($termTaxService->delete($id)));
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

    /**
     * @param TermTaxonomyProto $proto
     */
    protected function assertProtoContainsRequiredKeys (TermTaxonomyProto $proto) {
        $requiredKeys = $proto->getAllowedKeysForProto();
        $rsltKeys = array_keys($proto->toArray());
        foreach ($rsltKeys as $key) {
            $this->assertEquals(true, in_array($key, $requiredKeys));
        }
    }

    protected function assertCorrectProtoClass ($proto) {
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\TermTaxonomyProto', $proto);
    }

    protected function assertCorrectResultSetClass ($rsltSet) {
        $this->assertInstanceOf('\\Zend\\Db\\ResultSet\\ResultSet', $rsltSet);
    }

    public static function tearDownAfterClass () {
    }

}
