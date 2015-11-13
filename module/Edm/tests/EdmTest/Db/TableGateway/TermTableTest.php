<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/11/2015
 * Time: 1:44 PM
 * @requires edm-db-mysql (installed) with local credentials for it (local.php etc.)
 */

namespace EdmTest\Db\TableGateway;

use EdmTest\Bootstrap,
    Edm\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class TermTableTest.
 * Basic test to ensure that correct classes are being used by Edm\Db\TableGateway\TermTable.
 * Note** Full tests are not needed since TermTable's existing methods will be removed.
 * and it itself is pretty much just wrapping Zend\Db\TableGateway\TableGateway and nothing more.
 * @package EdmTest\Db\TableGateway
 */
class TermTableTest extends \PHPUnit_Framework_TestCase  {

    use ServiceLocatorAwareTrait;

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
    public static $test_term_group_alias = '__term-table-test-group__';

    public static $termTable;

    public static function setUpBeforeClass () {
        $locator = Bootstrap::getServiceManager();
        self::$termTable = $locator->get('Edm\Db\TableGateway\TermTable');
        self::$termTable->setServiceLocator($locator);
    }

    public function setUp () {
        $this->insertDbTestEntries();
    }

    public function testTermTableExistence () {
        // Ensure that is of expected type
        $this->assertInstanceOf('Edm\Db\TableGateway\TermTable',
            $this->termTable(),
            'Return value should match class Edm\Db\TableGateway\TermTable');

        // Ensure that abstract table is extended
        $this->assertInstanceOf('Edm\Db\TableGateway\BaseTableGateway',
            $this->termTable());

        // Ensure that 'Zend\Db\TableGateway\TableGateway' is being extended
        $this->assertInstanceOf('Zend\Db\TableGateway\TableGateway',
            $this->termTable());
    }

    public function testInsert () {
        // Ensure our test items are not in our table
        $this->deleteDbTestEntries();

        // Get table
        $termTable = $this->termTable();

        // Create our items
        $this->insertDbTestEntries();

        // Do a query for our created item
        $resultSet = $termTable->select(['term_group_alias' => self::$test_term_group_alias]);

        // Test that our created item is fetchable/exists-in-table:
        $this->assertCount(3, $resultSet, 'Should have "' + count($resultSet) + '" items created for test.');

        // Falsy result set
        $resultSet = $termTable->select(['term_group_alias' => 'hello_world']);

        // Falsy test
        $this->assertCount(0, $resultSet, 'Should have "0" items in result set.');
    }

    public function testSelectAndResultSet () {
        // Get table
        $termTable = $this->termTable();

        // Do a query for our created item
        $resultSet = $termTable->select(['term_group_alias' => self::$test_term_group_alias]);

        // Test result set interface:
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $resultSet,
            'Should return a "Zend\Db\ResultSet\ResultSet".');

        // Test that our created item is fetchable/exists-in-table:
        $this->assertCount(3, $resultSet, 'Should have "' + count($resultSet) + '" items created for test.');

        // Falsy result set
        $resultSet = $termTable->select(['term_group_alias' => 'hello_world']);

        // Falsy test
        $this->assertCount(0, $resultSet, '`$resultSet` should have "0" items in it.');
        $this->assertEquals(false, $resultSet->current(), '`current()` should return `false` when no items in result set.');

        // @note Other methods of ResultSet will not be tested here as ResultSet is a ZF component (which is already tested)
    }

    public function testResultSetProto () {
        // Get table
        $termTable = $this->termTable();

        // Do a query for our created item
        $resultSet = $termTable->select(['term_group_alias' => self::$test_term_group_alias]);

        // Assert the appropriate type is being returned for fetched, created item:
        $this->assertInstanceOf('Edm\\Db\\ResultSet\\Proto\\TermProto', $resultSet->current(),
            'Should return an object of type "Edm\\Db\\ResultSet\\Proto\\TermProto');
    }

    public function testResultSetProtoInputFilters () {
        // Get table
        $termTable = $this->termTable();

        // Do a query for our created item
        $resultSet = $termTable->select(['term_group_alias' => self::$test_term_group_alias]);

        // Ensure that fetched items are valid 'Term' objects:
        foreach($resultSet as $item) {
            $filter = $item->getInputFilter();
            $filter->setData($item);
            $this->assertEquals($filter->isValid(), true);
        }
    }

    public function testDelete () {
        // Get table
        $termTable = $this->termTable();

        // Delete test items from table
        $this->deleteDbTestEntries();

        // Do a query for our created item
        $resultSet = $termTable->select(['term_group_alias' => self::$test_term_group_alias]);

        // Test that our created item is fetchable/exists-in-table:
        $this->assertCount(0, $resultSet, 'Should have "' + count($resultSet) + '" items created for test.');
    }

    public function termTable () {
        return self::$termTable;
    }

    public function deleteDbTestEntries () {
        $this->termTable()->delete(['term_group_alias' => self::$test_term_group_alias]);
        return $this;
    }

    public function insertDbTestEntries () {
        foreach ($this->itemsToCreate as $item) {
            $this->termTable()->insert($item);
        }
        return $this;
    }

    public function tearDown () {
        $this->deleteDbTestEntries();
    }

    public static function tearDownAfterClass() {
        self::$termTable->delete(['term_group_alias' => self::$test_term_group_alias]);
        self::$termTable = null;
    }

}
