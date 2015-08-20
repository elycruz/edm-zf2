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

    protected function setUp() {
        $this->setServiceLocator(Bootstrap::getServiceManager());
    }

    public function testGetById () {
        $this->assertCount(10, $this->termTaxonomyService()->getById(1));
    }

    public function testGetByAlias () {
        $this->assertCount(10, $this->termTaxonomyService()->getByAlias('taxonomy'));
    }

    public function testSetListOrderId () {
        $this->termTaxonomyService()->setListOrderForId(1, 1000);
        $this->assertEquals($this->termTaxonomyService()->getById(1)['listOrder'], 1000,
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