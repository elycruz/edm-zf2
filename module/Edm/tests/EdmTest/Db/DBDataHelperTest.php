<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/13/2015
 * Time: 6:44 PM
 */

namespace EdmTest\Db;

use Edm\Db\DbDataHelper;

class DBDataHelperTest  extends \PHPUnit_Framework_TestCase  {

    public static $dbDataHelper;

    public static function setUpBeforeClass () {
        self::$dbDataHelper = new DbDataHelper();
    }

    /**
     * @return \Edm\Db\DbDataHelper
     */
    public function dbDataHelper () {
        return self::$dbDataHelper;
    }

    public function megaEscapeStringTestProvider () {
        return [
            [[
                'value' => 'hello%world',
                'escapedValue' => 'hello\\%world'
            ]],
            [[
                'value' => 'someValue;',
                'escapedValue' => 'someValue\\;'
            ]],
            [[
                'value' => "some' other' value",
                'escapedValue' => "some\\' other\\' value"
            ]],
            [[
                'value' => " \\ \\ \\ \\ ",
                'escapedValue' => " \\%5C \\%5C \\%5C \\%5C "
            ]],
            [[
                'value' => "Not needing escape.",
                'escapedValue' => "Not needing escape."
            ]],
            [[
                'value' => "All your base are belong to us.",
                'escapedValue' => "All your base are belong to us."
            ]],
            [[
                'value' => ";All ;your ;base ;are ;belong ;to ;us.",
                'escapedValue' => "\\;All \\;your \\;base \\;are \\;belong \\;to \\;us."
            ]]
        ];
    }

    /**
     * @dataProvider megaEscapeStringTestProvider
     * @param array $testCase
     */
    public function test_mega_escape_string ($testCase) {
        $dbDataHelper = $this->dbDataHelper();
        $escapedValue = $dbDataHelper->mega_escape_string($testCase['value']);
        $this->assertEquals($escapedValue, $testCase['escapedValue']);
    }

    /**
     * @dataProvider megaEscapeStringTestProvider
     * @param array $testCase
     */
    public function test_reverse_mega_escape_string ($testCase) {
        $dbDataHelper = $this->dbDataHelper();
        $unescapedValue = $dbDataHelper->reverse_mega_escape_string($testCase['escapedValue']);
        $this->assertEquals($unescapedValue, $testCase['value']);
    }

    public function escapeTupleTestProvider () {
        return [
            [[
                'field1' => 'All your\' base are\' belong to\' us.',
                'field2' => 'All your base are\' belong to us.',
                'field3' => ';All ;your\' ;base ;are\' ;%%% ;belong ;to ;us.',
                'field4' => " \\ \\ \\ \\ ",
            ], [
                'field1' => 'All your\\\' base are\\\' belong to\\\' us.',
                'field2' => 'All your base are\\\' belong to us.',
                'field3' => '\\;All \\;your\\\' \\;base \\;are\\\' \\;\\%\\%\\% \\;belong \\;to \\;us.',
                'field4' => " \\%5C \\%5C \\%5C \\%5C "
            ]]
        ];
    }

    public function escapeTuplesTestProvider () {
        return [
            [[[
                'field1' => 'All your\' base are\' belong to\' us.',
                'field2' => 'All your base are\' belong to us.',
                'field3' => ';All ;your\' ;base ;are\' ;%%% ;belong ;to ;us.',
                'field4' => " \\ \\ \\ \\ ",
            ],[
                'field1' => 'All your\' base are\' belong to\' us.',
                'field2' => 'All your base are\' belong to us.',
                'field3' => ';All ;your\' ;base ;are\' ;%%% ;belong ;to ;us.',
                'field4' => " \\ \\ \\ \\ ",
            ],[
                'field1' => 'All your\' base are\' belong to\' us.',
                'field2' => 'All your base are\' belong to us.',
                'field3' => ';All ;your\' ;base ;are\' ;%%% ;belong ;to ;us.',
                'field4' => " \\ \\ \\ \\ ",
            ]], [[
                'field1' => 'All your\\\' base are\\\' belong to\\\' us.',
                'field2' => 'All your base are\\\' belong to us.',
                'field3' => '\\;All \\;your\\\' \\;base \\;are\\\' \\;\\%\\%\\% \\;belong \\;to \\;us.',
                'field4' => " \\%5C \\%5C \\%5C \\%5C "
            ],[
                'field1' => 'All your\\\' base are\\\' belong to\\\' us.',
                'field2' => 'All your base are\\\' belong to us.',
                'field3' => '\\;All \\;your\\\' \\;base \\;are\\\' \\;\\%\\%\\% \\;belong \\;to \\;us.',
                'field4' => " \\%5C \\%5C \\%5C \\%5C "
            ],[
                'field1' => 'All your\\\' base are\\\' belong to\\\' us.',
                'field2' => 'All your base are\\\' belong to us.',
                'field3' => '\\;All \\;your\\\' \\;base \\;are\\\' \\;\\%\\%\\% \\;belong \\;to \\;us.',
                'field4' => " \\%5C \\%5C \\%5C \\%5C "
            ]]]
        ];
    }

    /**
     * Test `escapeTuple` method.
     * @param array $tuple
     * @param array $expectedTuple
     * @dataProvider escapeTupleTestProvider
     */
    public function testEscapeTuple ($tuple, $expectedTuple) {
        $escapedTuple = $this->dbDataHelper()->escapeTuple($tuple);

        // Array object version of `$tuple`
        $arrayObjectTuple = new \ArrayObject();
        $expectedArrayObject = new \ArrayObject();

        $keys = array_keys($tuple);

        // Test escaped tuple and populate array object version of it
        foreach ($keys as $key) {
            $this->assertEquals($expectedTuple[$key], $escapedTuple[$key],
                'Tuple should have key "' . $key . '".');
            $arrayObjectTuple->{$key} = $tuple[$key];
            $expectedArrayObject->{$key} = $expectedTuple[$key];
        }

        // Escape array object tuple
        $escapedArrayObject = $this->dbDataHelper()->escapeTuple($tuple);

        // Test escaped array object
        foreach ($arrayObjectTuple as $key => $value) {
            $this->assertEquals($expectedArrayObject->{$key}, $escapedArrayObject->{$key});
        }
    }

    /**
     * Test `escapeTuples` method.
     * @param array $tuples
     * @param array $expectedTuples
     * @dataProvider escapeTuplesTestProvider
     */
    public function testEscapeTuples ($tuples, $expectedTuples) {

    }
    
    /**
     * Test `reverseEscapeTuple` method.
     * @param array $tuple
     * @param array $expectedTuple
     * @dataProvider escapeTupleTestProvider
     */
    public function testReverseEscapeTuple ($tuple, $expectedTuple) {
        $reversedEsc = $this->dbDataHelper()->reverseEscapeTuple($expectedTuple);

        // Array object version of `$tuple`
        $originalUnescapedObj = new \ArrayObject();

        // Array object version of `$expectedTuple`
        $expectedArrayObject = new \ArrayObject();

        // Get keys to check
        $expectedTupleKeys = array_keys($expectedTuple);

        // Test reverse-escaped tuple and populate array object version of it
        foreach ($expectedTupleKeys as $key) {
            $this->assertEquals($reversedEsc[$key], $tuple[$key],
                'Tuple should have key "' . $key . '".');
            $originalUnescapedObj->{$key} = $tuple[$key];
            $expectedArrayObject->{$key} = $expectedTuple[$key];
        }

        // Reverse escape array object tuple
        $revEscObj = $this->dbDataHelper()->reverseEscapeTuple($expectedArrayObject);

        // Test reverse-escaped array object
        foreach ($originalUnescapedObj as $key => $value) {
            $this->assertEquals($revEscObj->{$key}, $originalUnescapedObj->{$key});
        }
    }

    /**
     * Test `reverseEscapeTuples` method.
     * @param array $tuples
     * @param array $expectedTuples
     * @dataProvider escapeTuplesTestProvider
     */
    public function testReverseEscapeTuples ($tuples, $expectedTuples) {

    }

}