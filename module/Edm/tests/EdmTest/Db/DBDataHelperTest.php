<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/13/2015
 * Time: 6:44 PM
 */

namespace EdmTest\Db;

use Edm\Db\DbDataHelper;

class DbDataHelperTest  extends \PHPUnit_Framework_TestCase  {

    /**
     * @var \Edm\Db\DbDataHelper
     */
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

    // @todo  Add json strings to data provider
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
    
    // @todo  Add json strings to data provider
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

    // @todo  Add json strings to data provider
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

    public function escapeTupleTestProxy ($tuple, $expectedTuple) {
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
    
    public function reverseEscapeTupleTestProxy ($tuple, $expectedTuple) {
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

    /**
     * Test `escapeTuple` method.  Should work on both `ArrayObject`'s and `Array`s.
     * @param array $tuple
     * @param array $expectedTuple
     * @dataProvider escapeTupleTestProvider
     */
    public function testEscapeTuple ($tuple, $expectedTuple) {
        $this->escapeTupleTestProxy($tuple, $expectedTuple);
    }

    /**
     * Test `escapeTuples` method.
     * @param array $tuples
     * @param array $expectedTuples
     * @dataProvider escapeTuplesTestProvider
     */
    public function testEscapeTuples ($tuples, $expectedTuples) {
        $tuplesLength = count($tuples);
        $this->assertTrue($tuplesLength === count($expectedTuples));
        for ($i = 0; $i < $tuplesLength; $i += 1) {
            $tuple = $tuples[$i];
            $expectedTuple = $expectedTuples[$i];
            $tupleLength = count($tuple);
            $expectedTupleLength = count($expectedTuple);
            $this->assertTrue($tupleLength === $expectedTupleLength);
            $this->escapeTupleTestProxy($tuple, $expectedTuple);
        }
    }
    
    /**
     * Test `reverseEscapeTuple` method.
     * @param array $tuple
     * @param array $expectedTuple
     * @dataProvider escapeTupleTestProvider
     */
    public function testReverseEscapeTuple ($tuple, $expectedTuple) {
        $this->reverseEscapeTupleTestProxy($tuple, $expectedTuple);
    }

    /**
     * Test `reverseEscapeTuples` method.
     * @param array $tuples
     * @param array $expectedTuples
     * @dataProvider escapeTuplesTestProvider
     */
    public function testReverseEscapeTuples ($tuples, $expectedTuples) {
        $tuplesLength = count($tuples);
        $this->assertTrue($tuplesLength === count($expectedTuples));
        for ($i = 0; $i < $tuplesLength; $i += 1) {
            $tuple = $tuples[$i];
            $expectedTuple = $expectedTuples[$i];
            $tupleLength = count($tuple);
            $expectedTupleLength = count($expectedTuple);
            $this->assertTrue($tupleLength === $expectedTupleLength);
            $this->reverseEscapeTupleTestProxy($tuple, $expectedTuple);
        }
    }
    
    /**
     * @dataProvider escapeTuplesTestProvider
     * @param array $tuple
     * @param array $expectedTuple
     */
    public function testJsonEncodeAndEscapeArray ($tuple, $expectedTuple) {
        $dbDataHelper = $this->dbDataHelper();
        $jsonString = $dbDataHelper->jsonEncodeAndEscapeArray($tuple);
        //$unEscapedJson = $dbDataHelper->unEscapeAndJsonDecodeString($jsonString);
        $this->assertTrue(strlen($jsonString) > 0, 'Assert JSON string has a length greater than 0.');
        $this->assertTrue(preg_match('/^\[\{/', $jsonString) === 1, 'Assert json has expected opening delimiters.');
        $this->assertTrue(preg_match('/\}\]$/', $jsonString) === 1, 'Assert json has expected closing delimiters.');
        /*$keys = array_keys($tuple);
        foreach ($keys as $key) {
            $valueType = gettype($tuple[$key]);
            $unEscapedValueType = gettype($unEscapedJson[$key]);
            $this->assertEquals($valueType, $unEscapedValueType,
                'Assert typeof value unescaped matches original value type (type before escaped).');
        }*/
    }
    
    /**
     * @dataProvider escapeTuplesTestProvider
     * @param array $tuple
     * @param array $expectedTuple
     */
    public function testUnEscapeAndJsonDecodeString ($tuple, $expectedTuple) {
        $dbDataHelper = $this->dbDataHelper();
        $jsonString = '[{\"hi\": \"ola\"}, {\"hello\": \"world\"}, '
                . '{\"all\": {\"your\": {\"base\": {\"are\": {\"belong\": {\"to\": {\"us\": true}}}}}}}]';
        $item3ExpectedKeys = ['all', 'your', 'base', 'are', 'belong', 'to', 'us'];
        $unEscapedJson = $dbDataHelper->unEscapeAndJsonDecodeString($jsonString);
        
        // Assert expected  objects are returned in first array
        foreach($unEscapedJson as $tuple) {
            $this->assertTrue(gettype($tuple) === 'array');
        }
        
        // Assert object one from JSON string
        $this->assertArrayHasKey ('hi', $unEscapedJson[0]);
        $this->assertEquals ('ola', $unEscapedJson[0]['hi']);
        
        // Assert object two from JSON string
        $this->assertArrayHasKey ('hello', $unEscapedJson[1]);
        $this->assertEquals('world', $unEscapedJson[1]['hello']);
        
        function inlineRecursiveCheck ($element, $index = 0, $keysToCheck, $self) {
            foreach($element as $key => $value) {
                $self->assertEquals($keysToCheck[$index], $key);
                // If not at the end of `keysToCheck` assume every object is an array (as is defined above).
                if ($index !== count($keysToCheck) - 1) {
                    $self->assertInternalType('array', $value);
                }
                // Else assert hardcoded, predefined final nested value is true
                else {
                    $self->assertTrue($value);
                }
                // If `value` is an array rercursively check it
                if (is_array($value)) {
                    inlineRecursiveCheck($value, $index + 1, $keysToCheck, $self);
                }
            }
        }
        
        // Recursively check element 3 of unencoded json array.
        inlineRecursiveCheck($unEscapedJson[2], 0, $item3ExpectedKeys, $this);
    }

}