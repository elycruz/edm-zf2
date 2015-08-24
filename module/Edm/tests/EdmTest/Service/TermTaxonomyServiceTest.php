<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 8/12/2015
 * Time: 11:27 PM
 */

namespace EdmTest\Service;

use Edm\Model\Term;
use Edm\Model\TermTaxonomy;
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
        $this->removeCreatedDbEntries();
    }

    public function removeCreatedDbEntries () {
        $service = $this->termTaxonomyService();
        $rowToDelete = $service->getByAlias('hello-world', 'uncategorized')->current();
        $row2ToDelete = $service->getByAlias('hello-world2', 'uncategorized')->current();
        if ($rowToDelete) {
            $this->termTaxonomyService()->deleteItem($rowToDelete->term_taxonomy_id);
        }
        if ($row2ToDelete) {
            $this->termTaxonomyService()->deleteItem($row2ToDelete->term_taxonomy_id);
        }
    }

    public function testGetById () {
        $this->assertEquals(11, $this->termTaxonomyService()->getById(1)->getFieldCount());
    }

    public function testGetByAlias () {
        $row = $this->termTaxonomyService()->getByAlias('taxonomy')->current();
        foreach ($this->props as $key => $value) {
            $this->assertArrayHasKey($value, $row);
        }
    }

    public function testSetListOrderId () {
        $this->termTaxonomyService()->setListOrderForId(1, 1000);
        $result = $this->termTaxonomyService()->getById(1)->current();
        $this->assertEquals($result->listOrder, 1000,
            'Updated list order should be updated in database.');
    }

    public function testGetSelect () {
        $this->assertInstanceOf('Zend\Db\Sql\Select',
            $this->termTaxonomyService()->getSelect());
    }

    public function testRead () {
        // Get service
        $service = $this->termTaxonomyService();

        // Fetch existing matches
        $resultSet = $service->read(
            array('where' => array('termTax.taxonomy' => 'user-group'))
        );

        // Test result set interface
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $resultSet,
            'Should return a "Zend\Db\ResultSet\ResultSet".');

        // Existing row matches count
        $this->assertEquals($resultSet->count(), 9,
            'Should select the exact number for items with taxonomy "user-group".');

        // Fetch non existing matches
        $resultSet = $service->read(array('where' => array('termTax.taxonomy' => 'hello-world')));

        // Non existing row matches count
        $this->assertEquals($resultSet->count(), 0,
            'Should select "0" rows when no matches are found.');
    }

    public function testCreateItem () {
        $self = $this;
        $service = $this->termTaxonomyService();
        $data = array(
            'term' => [
                'name' => 'Hello World',
                'alias' => 'hello-world'
            ],
            'term-taxonomy' => [
                'term_alias' => 'hello-world',
                'taxonomy' => 'uncategorized',
                'description' => 'None.'
            ]
        );
        $service->createItem($data)
                ->then(function ($id) use ($service, $self) {
                    $self->assertEquals(1, $service->getById($id)->count());
                }, function ($reason) use ($service, $self) {
                    $self->assertInstanceOf('\Exception', $reason);
                });
    }
//
//    public function testUpdateItem () {
//        $service = $this->termTaxonomyService();
//        $data = array(
//            'term' => [
//                'name' => 'Hello World 2',
//                'alias' => 'hello-world2'
//            ],
//            'term-taxonomy' => [
//                'term_alias' => 'hello-world2',
//                'taxonomy' => 'uncategorized',
//                'description' => 'None.'
//            ]
//        );
//        $service->createItem($data)
//            ->then(function ($id) use ($service) {
//                $data = array(
//                    'term' => [
//                        'name' => 'Ola Mundo',
//                        'alias' => 'hello-world2'
//                    ],
//                    'term-taxonomy' => [
//                        'term_alias' => 'hello-world2',
//                        'taxonomy' => 'uncategorized',
//                        'description' => ''
//                    ]
//                );
//                return $service->createItem($id, $data);
//            })
//            ->then(function ($id) use ($service) {
//                $this->assertEquals('Ola Mundo', $service->getById($id)->current()->term_name);
//            }, function ($reason) {
//                var_dump($reason);
//            });
//
//
//    }
//
//    public function testDeleteItem () {
//        $service = $this->termTaxonomyService();
//        $rowToDelete = $service->getByAlias('hello-world', 'uncategorized')->current();
//        $this->termTaxonomyService()->deleteItem($rowToDelete->term_taxonomy_id);
//    }

    public function testGetTermFormData () {
        $this->termTaxonomyService()
            ;
    }

    public function testGetTermTaxonomyTable () {
        $this->assertInstanceOf('Edm\Db\Table\TermTaxonomyTable',
            $this->termTaxonomyService()->getTermTaxonomyTable(),
            'Return value should match class Edm\Db\Table\TermTaxonomyTable');
    }

    public function testGetTermTable () {
        $this->assertInstanceOf('Edm\Db\Table\TermTable',
            $this->termTaxonomyService()->getTermTable(),
            'Return value should match class Edm\Db\Table\TermTable');
    }

    public function testDbConnection () {
        $dbAdapter = $this->getUserService()->getDb();
        $this->assertInstanceOf('Zend\Db\Adapter\Adapter', $dbAdapter);
        $schema = $dbAdapter->getDriver()->getConnection()->getCurrentSchema();
        $this->assertEquals('edm', $schema);
    }

    protected function tearDown () {
//        $this->removeCreatedDbEntries();
        $this->termTaxonomyService()->setListOrderForId(1, 1);
    }
}