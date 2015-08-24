<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 8/12/2015
 * Time: 11:27 PM
 */

namespace EdmTest\Service;

use EdmTest\Bootstrap,
    Edm\Service\TermTaxonomyServiceAwareTrait,
    Edm\Service\UserServiceAwareTrait,
    Edm\ServiceManager\ServiceLocatorAwareTrait;

class TermTaxonomyServiceTest extends \PHPUnit_Framework_TestCase {

    use TermTaxonomyServiceAwareTrait,
        UserServiceAwareTrait,
        ServiceLocatorAwareTrait;

    public $props = array(
        'term_taxonomy_id',
        'term_alias',
        'taxonomy',
        'description',
        'childCount',
        'assocItemCount',
        'listOrder',
        'parent_id',
            // Joined keys
        'term_name',
        'term_group_alias'
        );

    protected function setUp() {
        $this->setServiceLocator(Bootstrap::getServiceManager());
    }

    public function testGetById () {
        $this->assertEquals(11, $this->termTaxonomyService()->getById(1)->getFieldCount());
    }

    public function testGetByAlias () {
        $rslt = $this->termTaxonomyService()->getByAlias('taxonomy')->current();
        foreach ($this->props as $key => $value) {
            $this->assertArrayHasKey($value, $rslt);
        }
    }

    public function testSetListOrderId () {
        $this->termTaxonomyService()->setListOrderForId(1, 1000);
        $result = $this->termTaxonomyService()->getById(1)->current();
        $this->assertEquals($result->listOrder, 1000,
            'Updated list order should be updated in database.');
    }

    public function testGetSelect () {
        $this->termTaxonomyService()
            ;
    }

    public function createItem () {
        $this->termTaxonomyService()
            ;
    }

    public function updateItem () {
        $this->termTaxonomyService()
            ;
    }

    public function deleteItem () {
        $this->termTaxonomyService()
            ;
    }

    public function getTermFormData () {
        $this->termTaxonomyService()
            ;
    }

    public function getTermTaxonomyTable () {
        $this->termTaxonomyService()
            ;
    }

    public function getTermTable () {
        $this->termTaxonomyService()
            ;
    }

    public function testDbConnection () {
        $dbAdapter = $this->getUserService()->getDb();
        $this->assertInstanceOf('Zend\Db\Adapter\Adapter', $dbAdapter);
        $schema = $dbAdapter->getDriver()->getConnection()->getCurrentSchema();
        $this->assertEquals('edm', $schema);
    }

    protected function tearDown () {
        $this->termTaxonomyService()->setListOrderForId(1, 1);
    }
}