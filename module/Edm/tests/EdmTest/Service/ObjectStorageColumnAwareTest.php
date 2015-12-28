<?php
declare(strict_types=1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace EdmTest\Service;

use Edm\Service\ObjectStorageColumnAware,
    Edm\Service\ObjectStorageColumnAwareTrait,
    Edm\Db\DbDataHelperAware,
    Edm\Db\DbDataHelperAwareTrait;
        

class ExampleObjectStorageColumnAwareClass 
implements ObjectStorageColumnAware, DbDataHelperAware {
    use DbDataHelperAwareTrait,
        ObjectStorageColumnAwareTrait;
}

/**
 * Description of ObjectStorageColumnAwareTest
 *
 * @author Ely
 */
class ObjectStorageColumnAwareTest extends \PHPUnit_Framework_TestCase {
 
    //put your code here
    public function exampleTupleProvider () {
        return [
            [[ 
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
            ]]
        ];
    }
    
    public function exampleTuplesProvider () {
        return [
               [[[ 
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
            ],
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
            ]]]
        ];
    }
    
    /**
     * @dataProvider exampleTupleProvider
     * @param array $data
     */
    public function testSerializeAndEscapeTuple (array $data) {
        $objStorageAware = new ExampleObjectStorageColumnAwareClass();
        $serializedData = $objStorageAware->serializeAndEscapeTuple($data);
        $this->assertTrue(is_string($serializedData) && strlen($serializedData) > 0);
        $this->assertCount(count($data), unserialize($serializedData));
    }
    
    /**
     * @dataProvider exampleTuplesProvider
     * @param array $data
     */
    public function testSerializeAndEscapeTuples (array $data) {
        $objStorageAware = new ExampleObjectStorageColumnAwareClass();
        $serializedData = $objStorageAware->serializeAndEscapeTuples($data);
        $this->assertTrue(is_string($serializedData) && strlen($serializedData) > 0);
        $this->assertCount(count($data), unserialize($serializedData));
    }    
    
    /**
     * @dataProvider exampleTupleProvider
     * @param array $data
     */
    public function testUnSerializeAndUnEscapeTuple (array $data) {
        $objStorageAware = new ExampleObjectStorageColumnAwareClass();
        $serializedData = $objStorageAware->serializeAndEscapeTuple($data);
        $unserializedData = $objStorageAware->unSerializeAndUnescapeTuple($serializedData);
        $this->assertTrue(is_array($unserializedData));
        $this->assertCount(count($data), $unserializedData);
    }
    
    /**
     * @dataProvider exampleTuplesProvider
     * @param array $data
     */
    public function testUnSerializeAndUnEscapeTuples (array $data) {
        $objStorageAware = new ExampleObjectStorageColumnAwareClass();
        $serializedData = $objStorageAware->serializeAndEscapeTuples($data, null);
        $unserializedData = $objStorageAware->unSerializeAndUnescapeTuples($serializedData);
        $this->assertTrue(is_array($unserializedData));
        $this->assertCount(count($data), $unserializedData);        
//        for ($i = 0; $i <= count($data); $i += 1) {
//            $tuple = $data[$i];
//            $unserializedTuple = $unserializedData[$i];
//            $this->assertCount(count($tuple), $unserializedTuple);
//
//            foreach ($tuple as $key => $value) {
//                $this->assertEquals(gettype($unserializedTuple[$key]), gettype($value));
//            }
//        }
    } 
    
}
