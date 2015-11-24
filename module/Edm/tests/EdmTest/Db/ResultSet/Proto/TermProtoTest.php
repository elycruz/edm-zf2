<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/13/2015
 * Time: 11:58 AM
 */

namespace EdmTest\Db\ResultSet\Proto;

use Edm\Db\ResultSet\Proto\TermProto;

class TermProtoTest extends \PHPUnit_Framework_TestCase
{
    public $allowedKeysForProto = [
        'name', 'alias', 'term_group_alias'
    ];

    public $invalidKeys = [
        'hello', 'world', 'all', 'your', 'base', 'are', 'belong', 'to', 'us'
    ];

    public function forTruthyTestsProvider()
    {
        $numItems = 3;
        $out = [];
        for ($i = 0; $i < $numItems; $i += 1) {
            $out[] = [new TermProto([
                'name' => 'Well Structured Term ' . ($i + 1),
                'alias' => 'well-structured-term-' . ($i + 1),
                'term_group_alias' => '__edmtest-term-proto-test__'
            ])];
        }
        return $out;
    }

    public function forFalsyTestsProvider()
    {
        // Num items to generate
        $numItems = 3;

        // For export
        $out = [];

        // Set empty protos
        for ($i = 0; $i < $numItems; $i += 1) {

            // Initial term proto data
            $data = [
                'name' => null,
                'alias' => null,
                'term_group_alias' => null
            ];

            // Insert invalid keys for tests
            foreach ($this->invalidKeys as $invalidKey) {
                $data[$invalidKey] = null;
            }

            // Export term proto
            $out[] = [new TermProto($data)];
        }

        // Add one more proto with no values
        $out[] = [new TermProto()];

        // Return collection of collection of func arguments
        return $out;
    }

    /**
     * @dataProvider forTruthyTestsProvider
     * @param TermProto $item
     */
    public function testHasMethod ($item) {
        $allowedKeys = $this->allowedKeysForProto;
        foreach ($allowedKeys as $key) {
            $this->assertEquals(true, $item->has($key));
        }
    }

    /**
     * @dataProvider forFalsyTestsProvider
     * @param TermProto $item
     */
    public function testHasMethodFails ($item) {
        $allowedKeys = $this->allowedKeysForProto;
        foreach ($allowedKeys as $key) {
            $this->assertEquals(false, $item->has($key));
        }
    }

    /**
     * @dataProvider forTruthyTestsProvider
     * @param TermProto $item
     */
    public function testToArrayForTruthyValues($item)
    {
        // For `toArray` tests
        $rslt = $item->toArray();

        // Test for valid keys
        foreach ($this->allowedKeysForProto as $key) {
            $this->assertEquals(true, array_key_exists($key, $rslt),
                'Term proto\'s `toArray` method for this case should\'nt ' .
                'return an array with valid term proto keys.');
        }
    }

    /**
     * @dataProvider forFalsyTestsProvider
     * @param TermProto $item
     */
    public function testToArrayWithFalsyValues($item)
    {
        // Test empty `toArray` result
        $this->assertCount(0, $item->toArray());
    }

    /**
     * @dataProvider forTruthyTestsProvider
     * @param $item
     */
    public function testGetFormKey($item)
    {
        $this->assertEquals($item->getFormKey(), 'term');
    }

    public function testGetAllowedKeysForProto()
    {
        $termProto = new TermProto();
        $protoValidKeys = $termProto->getAllowedKeysForProto();
        foreach ($this->allowedKeysForProto as $key) {
            $this->assertEquals(true, in_array($key, $protoValidKeys),
                'A term proto should contain key "' . $key . '".');
        }
    }

    public function testToArray()
    {
        $termProto = new TermProto();
        $protoValidKeys = $termProto->getAllowedKeysForProto();
        foreach ($this->allowedKeysForProto as $key) {
            $this->assertEquals(true, in_array($key, $protoValidKeys),
                'A term proto should contain key "' . $key . '".');
        }
    }

    public static function tearDownAfterClass()
    {
    }

}
