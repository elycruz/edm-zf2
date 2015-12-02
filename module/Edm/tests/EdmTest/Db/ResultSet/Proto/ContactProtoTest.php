<?php

namespace EdmTest\Db\ResultSet\Proto;

use Edm\Db\ResultSet\Proto\ContactProto;

class ContactProtoTest extends \PHPUnit_Framework_TestCase {
    public function testGetAllowedKeysForProto () {
        $proto = new ContactProto();
        $this->assertArraySubset([
            'contact_id',
            'parent_id',
            'name',
            'firstName',
            'middleName',
            'lastName',
            'email',
            'altEmail',
            'type',
            'userParams'
        ], $proto->getAllowedKeysForProto());
    }

    public function testFormKey () {
        $proto = new ContactProto();
        $this->assertEquals('contact', $proto->getFormKey());
    }

    public function testNotAllowedKeysForUpdate () {
        $proto = new ContactProto();
        $this->assertArraySubset(['contact_id', 'email'], $proto->getNotAllowedKeysForUpdate());
    }

    public function testGetInputFilter () {
        $proto = new ContactProto();
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $proto->getInputFilter());
    }
}

