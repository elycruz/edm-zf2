<?php

namespace EdmTest\Db\ResultSet\Proto;

use Edm\Db\ResultSet\Proto\PostCategoryRelProto;

class PostCategoryRelProtoTest extends \PHPUnit_Framework_TestCase {
    
    public function testGetAllowedKeysForProto () {
        $proto = new PostCategoryRelProto();
        $this->assertArraySubset([
            'post_id',
            'term_taxonomy_id'
        ], $proto->getAllowedKeysForProto());
    }

    public function testFormKey () {
        $proto = new PostCategoryRelProto();
        $this->assertEquals('postCategoryRel', $proto->getFormKey());
    }

    public function testNotAllowedKeysForUpdate () {
        $proto = new PostCategoryRelProto();
        $this->assertArraySubset(['post_id'], $proto->getNotAllowedKeysForUpdate());
    }

    public function testGetInputFilter () {
        $proto = new PostCategoryRelProto();
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $proto->getInputFilter());
    }
}

