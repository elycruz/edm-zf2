<?php

namespace EdmTest\Db\ResultSet\Proto;

use Edm\Db\ResultSet\Proto\DateInfoProto;

class DateInfoProtoTest extends \PHPUnit_Framework_TestCase {
    public function testGetAllowedKeysForProto () {
        $proto = new DateInfoProto();
        $this->assertArraySubset([
            'date_info_id',
            'createdDate',
            'createdById',
            'lastUpdated',
            'lastUpdatedById',
            'checkedInDate',
            'checkedInById',
            'checkedOutDate',
            'checkedOutById',
        ], $proto->getAllowedKeysForProto());
    }

    public function testFormKey () {
        $proto = new DateInfoProto();
        $this->assertEquals('dateInfo', $proto->getFormKey());
    }

    public function testNotAllowedKeysForUpdate () {
        $proto = new DateInfoProto();
        $this->assertArraySubset(['date_info_id'], $proto->getNotAllowedKeysForUpdate());
    }

    public function testGetInputFilter () {
        $proto = new DateInfoProto();
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $proto->getInputFilter());
    }
}

