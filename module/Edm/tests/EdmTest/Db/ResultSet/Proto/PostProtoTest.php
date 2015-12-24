<?php

namespace EdmTest\Db\ResultSet\Proto;

use Edm\Db\ResultSet\Proto\PostProto;

class PostProtoTest extends \PHPUnit_Framework_TestCase {
    public function testGetAllowedKeysForProto () {
        $proto = new PostProto();
        $this->assertArraySubset([
            'post_id',
            'parent_id',
            'title',
            'alias',
            'content',
            'excerpt',
            'hits',
            'listOrder',
            'commenting',
            'commentCount',
            'type',
            'accessGroup',
            'status',
            'userParams',
            'date_info_id'
        ], $proto->getAllowedKeysForProto());
    }

    public function testFormKey () {
        $proto = new PostProto();
        $this->assertEquals('post', $proto->getFormKey());
    }

    public function testNotAllowedKeysForUpdate () {
        $proto = new PostProto();
        $this->assertArraySubset(['post_id', 'date_info_id'], $proto->getNotAllowedKeysForUpdate());
    }

    public function testGetInputFilter () {
        $proto = new PostProto();
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $proto->getInputFilter());
    }
}

