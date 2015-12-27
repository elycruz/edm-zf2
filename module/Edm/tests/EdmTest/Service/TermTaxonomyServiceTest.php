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

    public function testCreateTermTaxonomy () {
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
        
        // Create proto
        $termTaxonomyProto = new TermTaxonomyProto();
        $termTaxonomyProto->exchangeNestedArray($data);

        // Create test term taxonomy
        $term_taxonomy_id = $this->termTaxService()->createTermTaxonomy($termTaxonomyProto);
        $termTaxonomy = $this->termTaxService()->getTermTaxonomyById($term_taxonomy_id);
        
        // Delete inserted row
        $this->termTaxService()->deleteTermTaxonomy($termTaxonomy);

        // Assert an 'id' was returned from `create` process
        $this->assertEquals(true, is_numeric($term_taxonomy_id));
    }

    public function testUpdateTermTaxonomy () {
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
                
        // Create proto
        $termTaxonomyProto = new TermTaxonomyProto();
        $termTaxonomyProto->exchangeNestedArray($data);
        
        // Create test term taxonomy
        $term_taxonomy_id = $termTaxService->createTermTaxonomy($termTaxonomyProto);
        
        // Fetch and alter created term taxonomy
        $fetchedTermTaxonomy = $termTaxService->getTermTaxonomyById($term_taxonomy_id);
        $fetchedTermTaxonomy->description = 'Some description here.';
        $fetchedTermTaxonomy->accessGroup = 'user';
        $fetchedTermTaxonomy->getTermProto()->name = 'Some Term Taxonomy Hereio Bob';
        $fetchedTerm = $fetchedTermTaxonomy->getTermProto();

        // Record and fetch updates
        $updateResult = $termTaxService->updateTermTaxonomy($fetchedTermTaxonomy);
        $fetchedUpdatedTermTaxonomy = $termTaxService->getTermTaxonomyById($term_taxonomy_id);
        $fetchedUpdatedTerm = $fetchedUpdatedTermTaxonomy->getTermProto();

        // Delete inserted row
        $this->termTaxService()->deleteTermTaxonomy($fetchedTermTaxonomy);

        // Assert an 'id' was returned from `create` process
        $this->assertNotInstanceOf('\Exception', $updateResult, $updateResult);

        // Assert that updates were made
        $this->assertEquals($fetchedTermTaxonomy->description, $fetchedUpdatedTermTaxonomy->description);
        $this->assertEquals($fetchedTermTaxonomy->accessGroup, $fetchedUpdatedTermTaxonomy->accessGroup);
        $this->assertEquals($fetchedTerm->name, $fetchedUpdatedTerm->name);
        $this->assertEquals($fetchedTerm->term_group_alias, $fetchedUpdatedTerm->term_group_alias);
    }

    public function testDeleteTermTaxonomy () {
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
        
        // Create proto
        $termTaxonomyProto = new TermTaxonomyProto();
        $termTaxonomyProto->exchangeNestedArray($data);
        
        // Create test term taxonomy
        $term_taxonomy_id = $termTaxService->createTermTaxonomy($termTaxonomyProto);
        $termTaxonomy = $termTaxService->getTermTaxonomyById($term_taxonomy_id);
        $this->assertEquals(true, $termTaxService->deleteTermTaxonomy($termTaxonomy));
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
