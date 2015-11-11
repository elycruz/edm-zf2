<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/11/2015
 * Time: 1:44 PM
 * @requires edm-db-mysql (installed) with local credentials for it (local.php etc.)
 */

namespace EdmTest\Db\Table;

use EdmTest\Bootstrap,
    Edm\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class TermTableTest.
 * Basic test to ensure that correct classes are being used by Edm\Db\Table\TermTable.
 * Note** Full tests are not needed since TermTable's existing methods will be removed.
 * and it itself is pretty much just wrapping Zend\Db\TableGateway\TableGateway and nothing more.
 * @package EdmTest\Db\Table
 */
class TermTableTest extends \PHPUnit_Framework_TestCase  {

    use ServiceLocatorAwareTrait;

    /**
     * Term Table.
     * @var Edm\Db\Table\TermTable
     */
    protected $_termTable;

    /**
     * Items to create for truthy tests.
     * @var array<array[alias,name,term_group_alias]>
     */
    public $itemsToCreate = [
        [
            'alias' => 'fictional-term-group',
            'name' => 'Fictional Term Group',
            'term_group_alias' => '__term-table-test-group__'
        ],
        [
            'alias' => 'pretend-item-1',
            'name' => 'Pretend Item 1',
            'term_group_alias' => '__term-table-test-group__'
        ],
        [
            'alias' => 'pretend-item-2',
            'name' => 'Pretend Item 2',
            'term_group_alias' => '__term-table-test-group__'
        ]
    ];

    /**
     * Term group alias to use to insert, delete and update our test items.
     * @var string
     */
    public $test_term_group_alias = '__term-table-test-group__';

    protected function setUp() {
        $this->setServiceLocator(Bootstrap::getServiceManager());
    }

    public function termTable () {
        if (empty($this->_termTable)) {
            $locator = $this->getServiceLocator();
            $this->_termTable = $this->getServiceLocator()->get('Edm\Db\Table\TermTable');
            $this->_termTable->setServiceLocator($locator);
        }
        return $this->_termTable;
    }

    public function deleteDbTestEntries () {
        $this->termTable()->delete(['term_group_alias' => $this->test_term_group_alias]);
    }

    public function testTermTableAvailable () {
        // Ensure that is of expected type
        $this->assertInstanceOf('Edm\Db\Table\TermTable',
            $this->termTable(),
            'Return value should match class Edm\Db\Table\TermTable');

        // Ensure that abstract table is extended
        $this->assertInstanceOf('Edm\Db\Table\AbstractTable',
            $this->termTable());

        // Ensure that 'Zend\Db\TableGateway\TableGateway' is being extended
        $this->assertInstanceOf('Zend\Db\TableGateway\TableGateway',
            $this->termTable());
    }

    public function testInsertAndSelect () {
        // Get table
        $termTable = $this->termTable();

        // Create our items
        $this->insertDbTestEntries();

        // Do a query for our created item
        $resultSet = $termTable->select(['term_group_alias' => $this->test_term_group_alias]);

        // Test result set interface:
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $resultSet,
            'Should return a "Zend\Db\ResultSet\ResultSet".');

        // Test that our created item is fetchable/exists-in-table:
        $this->assertCount(3, $resultSet, 'Should have "' + count($resultSet) + '" items created for test.');

        // Assert the appropriate type is being returned for fetched, created item:
        $this->assertInstanceOf('Edm\Model\Term', $resultSet->current(),
            'Should return an object of type "Edm\Model\Term');

        // Ensure that fetched items are valid 'Term' objects:
        foreach($resultSet as $item) {
            $filter = $item->getInputFilter();
            $filter->setData($item);
            $this->assertEquals($filter->isValid(), true);
        }
    }

    public function insertDbTestEntries () {
        foreach ($this->itemsToCreate as $item) {
            $this->termTable()->insert($item);
        }
    }

    protected function tearDown () {
        $this->deleteDbTestEntries();
    }

}
