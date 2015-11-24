<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/23/2015
 * Time: 3:32 PM
 * @note Currently this test case class is serving as test case for both 'TermTaxonomyProto' and 'AbstractProto'.
 * @note test cases should be separated.
 * @todo Separate the `AbstractProto` tests' stuff from this test case.
 * @todo Use mock objects for `AbstractProto` tests.
 */

namespace EdmTest\Db\ResultSet\Proto;

use Edm\Db\ResultSet\Proto\ProtoInterface;
use Edm\Db\ResultSet\Proto\TermTaxonomyProto;
use Zend\Config\Config;
use Zend\InputFilter\InputFilter;

class TermTaxonomyProtoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    public $allowedKeysForProto = [
        'term_taxonomy_id',
        'term_alias',
        'taxonomy',
        'description',
        'accessGroup',
        'listOrder',
        'parent_id'
    ];

    /**
     * @var string
     */
    public $formKey = 'termTaxonomy';

    /**
     * @var array
     */
    public $defaultInputOptionKeys = [
        'id', 'int', 'alias', 'short-alias', 'name', 'short-name',
        'boolean', 'email', 'password', 'html_id', 'html_class',
        'description', 'screen-name', 'activation-key'
    ];

    /**
     * @return TermTaxonomyProto
     */
    public function fullyQualifiedTermTaxonomyProvider () {
        return [[
            new TermTaxonomyProto([
                'name' => 'Some Term Taxonomy',
                'alias' => 'some-term-taxonomy',
                'term_group_alias' => 'some-term-taxonomy-group',
                'term_taxonomy_id' => 1,
                'term_alias' => 'some-term-taxonomy',
                'taxonomy' => 'taxonomy',
                'accessGroup' => 'user',
                'description' => 'Some description.',
                'childCount' => 0,
                'assocItemCount' => 0,
                'listOrder' => 0,
                'parent_id' => 0
            ])
        ]];
    }

    /**
     * @return TermTaxonomyProto
     */
    public function emptyTermTaxonomyProvider () {
        return [[new TermTaxonomyProto()]];
    }

    /**
     * @return array
     */
    public function exchangeArrayTruthyProvider () {
        return [[
            [
                'name' => 'Some Term Taxonomy',
                'alias' => 'some-term-taxonomy',
                'term_group_alias' => 'some-term-taxonomy-group',
                'term_taxonomy_id' => 1,
                'term_alias' => 'some-term-taxonomy',
                'taxonomy' => 'taxonomy',
                'accessGroup' => 'user',
                'description' => 'Some description.',
                'childCount' => 0,
                'assocItemCount' => 0,
                'listOrder' => 0,
                'parent_id' => 0
            ]
        ]];
    }

    /**
     * @return array<[ [string], [string], [string] ]>
     */
    public function setAllowedKeysOnProtoProvider () {
        $refTermTaxonomy = new TermTaxonomyProto();
        $data = [];
        $allowedKeys = $refTermTaxonomy->getAllowedKeysForProto();
        $invalidKeys = ['a', 'e', 'i', 'o', 'u', 'and', 'some', 'times', 'y'];
        $i = 0;

        // Set valid keys
        foreach ($allowedKeys as $key) {
            $data[$key] = 'Some data here ' . $i;
            $i += 1;
        }

        // Set invalid keys
        foreach ($invalidKeys as $key) {
            $data[$key] = 'Invalid data here ' . $i;
            $i += 1;
        }

        return [[ $data, $allowedKeys, $invalidKeys ]];
    }

    /**
     * @dataProvider emptyTermTaxonomyProvider
     * @param $termTaxonomy
     */
    public function testGetFormKey (TermTaxonomyProto $termTaxonomy) {
        $this->assertEquals($this->formKey, $termTaxonomy->getFormKey());
    }

    /**
     * @dataProvider emptyTermTaxonomyProvider
     * @param $termTaxonomy
     */
    public function testGetAllowedKeysForProto (TermTaxonomyProto $termTaxonomy) {
        $this->assertArraySubset($this->allowedKeysForProto,
            $termTaxonomy->getAllowedKeysForProto());
    }

    /**
     * @dataProvider emptyTermTaxonomyProvider
     * @param $termTaxonomy
     */
    public function testGetSubProtoGetters (TermTaxonomyProto $termTaxonomy) {
        $this->assertArraySubset(['getTermProto', 'getTermTaxonomyProxyProto'],
            $termTaxonomy->getSubProtoGetters());
    }

    /**
     * @dataProvider emptyTermTaxonomyProvider
     * @param $termTaxonomy
     */
    public function testGetTermTaxonomyProxyProto (TermTaxonomyProto $termTaxonomy) {
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\TermTaxonomyProxyProto',
            $termTaxonomy->getTermTaxonomyProxyProto());
    }

    /**
     * @dataProvider emptyTermTaxonomyProvider
     * @param $termTaxonomy
     */
    public function testGetTermProto (TermTaxonomyProto $termTaxonomy) {
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\TermProto',
            $termTaxonomy->getTermProto());
    }

    /**
     * @dataProvider fullyQualifiedTermTaxonomyProvider
     * @param $termTaxonomy
     */
    public function testFilterArrayBasedOnOp (TermTaxonomyProto $termTaxonomy) {
        $rslt = $termTaxonomy->toArray();

        // Filter for insert
        $rsltForInsert = $termTaxonomy->filterArrayBasedOnOp(
            $rslt, ProtoInterface::FOR_OPERATION_DB_INSERT);
        $this->assertEquals(false, isset($rsltForInsert['term_taxonomy_id']));

        // Filter for update
        $rsltForUpdate = $termTaxonomy->filterArrayBasedOnOp(
            $rslt, ProtoInterface::FOR_OPERATION_DB_UPDATE);
        $this->assertEquals(false, isset($rsltForUpdate['term_taxonomy_id']));
    }

    /**
     * @dataProvider fullyQualifiedTermTaxonomyProvider
     * @param $termTaxonomy
     */
    public function testHasMethod (TermTaxonomyProto $termTaxonomy) {
        // Get empty term taxonomy for not has keys assertion
        $emptyTermTaxonomy = new TermTaxonomyProto();

        // Test has keys
        foreach ($this->allowedKeysForProto as $key) {
            $this->assertEquals(true, $termTaxonomy->has($key));
        }

        // Test not has keys
        foreach ($this->allowedKeysForProto as $key) {
            $this->assertEquals(false, $emptyTermTaxonomy->has($key));
        }
    }

    /**
     * @dataProvider exchangeArrayTruthyProvider
     * @param array $input
     */
    public function testExchangeArray (array $input) {
        // Get empty term taxonomy
        $termTaxonomy = new TermTaxonomyProto();

        // Exchange array
        $rslt = $termTaxonomy->exchangeArray($input);

        // Assert returned empty array
        $this->assertCount(0, $rslt);

        // Protos to get valid keys from
        $resultSetProtos = [];

        // Set term taxonomy proto to be verified
        $resultSetProtos[$this->formKey] = $termTaxonomy;

        // Get term proto
        $resultSetProtos['term'] = $termTaxonomy->getTermProto();

        // Get term taxonomy proxy proto
        $resultSetProtos['termTaxonomyProxy'] = $termTaxonomy->getTermTaxonomyProxyProto();

        // Test has keys
        foreach ($this->allowedKeysForProto as $key) {
            $this->assertEquals(true, $termTaxonomy->has($key));
        }

        // Validate keys exist for all sub protos of term taxonomy proto
        foreach ($resultSetProtos as $protoKey => $protoValue) {
            // Get allowed keys for proto
            $allowedKeysForDb = $protoValue->getAllowedKeysForProto();

            // Ensure expected set keys are set
            foreach ($allowedKeysForDb as $key) {
                // Assert key is set on sub proto
                $this->assertEquals(true, $protoValue->has($key));
            }
        }

        // Get some new input data
        $newInputData = []; $i = 0;
        foreach ($input as $key => $value) {
            $newInputData[$key] = 'New Value ' . $i;
            $i += 1;
        }

        // Add some more fields to term taxonomy
        $termTaxonomy->exchangeArray($newInputData);

        // Ensure invalid fields/keys are not set on term taxonomy proto
        foreach ($this->allowedKeysForProto as $key) {
            // Assert key is not set on proto
            $this->assertEquals(true, $termTaxonomy->has($key));
            $this->assertEquals($newInputData[$key], $termTaxonomy->{$key});
        }
    }

    /**
     * @dataProvider setAllowedKeysOnProtoProvider
     * @param array $data
     * @param array $allowedKeys
     * @param array $invalidKeys
     */
    public function testSetAllowedKeysOnProto (array $data, array $allowedKeys, array $invalidKeys) {
        // Get term taxonomy
        $termTaxonomy = new TermTaxonomyProto();

        // Set allowed keys on proto
        $rslt = $termTaxonomy->setAllowedKeysOnProto($data);

        // Assert method returns proto
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\TermTaxonomyProto', $rslt);

        // Assert proto has valid keys
        foreach ($allowedKeys as $key) {
            $this->assertEquals(true, $termTaxonomy->has($key));
        }

        // Assert proto doesn't have invalid keys
        foreach ($invalidKeys as $key) {
            $this->assertEquals(false, $termTaxonomy->has($key));
        }
    }

    /**
     * @dataProvider emptyTermTaxonomyProvider
     * @param $termTaxonomy
     */
    public function testGetInputFilter (TermTaxonomyProto $termTaxonomy) {
        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface',
            $termTaxonomy->getInputFilter());
    }

    /**
     * @dataProvider emptyTermTaxonomyProvider
     * @param $termTaxonomy
     */
    public function testSetInputFilter (TermTaxonomyProto $termTaxonomy) {
        $rslt = $termTaxonomy->setInputFilter(new InputFilter());
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\TermTaxonomyProto', $rslt);
        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface',
            $termTaxonomy->getInputFilter());
    }

    /**
     * @dataProvider fullyQualifiedTermTaxonomyProvider
     * @param TermTaxonomyProto $termTaxonomy
     */
    public function testToArrayNested (TermTaxonomyProto $termTaxonomy) {
        // Protos to get valid keys from
        $resultSetProtos = [];

        // Set term taxonomy proto to be verified
        $resultSetProtos[$this->formKey] = $termTaxonomy;

        // Get term proto
        $resultSetProtos['term'] = $termTaxonomy->getTermProto();

        // Get term taxonomy proxy proto
        $resultSetProtos['termTaxonomyProxy'] = $termTaxonomy->getTermTaxonomyProxyProto();

        // Dump nested array
        $result = $termTaxonomy->toArrayNested();

        // Assert the top level expected keys are set
        $this->assertArrayHasKey('term', $result);
        $this->assertArrayHasKey($this->formKey, $result);
        $this->assertArrayHasKey('termTaxonomyProxy', $result);

        // Validate keys exist for all sub arrays in result
        foreach ($resultSetProtos as $protoKey => $protoValue) {
            // Get allowed keys for proto
            $allowedKeysForDb = $protoValue->getAllowedKeysForProto();

            // Ensure expected set keys are set
            foreach ($allowedKeysForDb as $key) {
                // Assert key is set on result section
                $this->assertEquals(true, isset($result[$protoKey][$key]));
            }
        }
    }

    /**
     * @dataProvider fullyQualifiedTermTaxonomyProvider
     * @param TermTaxonomyProto $termTaxonomy
     */
    public function testToArray (TermTaxonomyProto $termTaxonomy) {
        // Get `toArray` result
        $rslt = $termTaxonomy->toArray();

        // Get allowed keys
        $allowedKeys = $termTaxonomy->getAllowedKeysForProto();

        // Ensure that keys are set directly on array and not nested
        foreach($allowedKeys as $key) {
            $this->assertEquals(true, isset($rslt[$key]));
        }
    }

    /*-------------------------------------------------------------------*
     * AbstractProto methods and statics
     * @note these tests will be moved out of here at a later date.
     * @todo move these tests out of here.
     *-------------------------------------------------------------------*/

    /**
     * @dataProvider emptyTermTaxonomyProvider
     * @param TermTaxonomyProto $termTaxonomy
     */
    public function testSetDefaultInputOptions (TermTaxonomyProto $termTaxonomy) {
        $oldInputOptions = TermTaxonomyProto::getDefaultInputOptions();
        TermTaxonomyProto::setDefaultInputOptions(new Config([]));
        $this->assertInstanceOf('Zend\Config\Config', TermTaxonomyProto::getDefaultInputOptions());
        $this->assertInstanceOf('Edm\InputFilter\DefaultInputOptionsAware', $termTaxonomy);
        TermTaxonomyProto::setDefaultInputOptions($oldInputOptions);
    }

    public function testGetDefaultInputOptions () {
        $this->assertInstanceOf('Zend\Config\Config', TermTaxonomyProto::getDefaultInputOptions());
    }

    public function testGetDefaultInputOptionsByKey () {
        foreach ($this->defaultInputOptionKeys as $key) {
            $this->assertEquals(true, is_array(TermTaxonomyProto::getDefaultInputOptionsByKey($key)));
        }
    }

}
