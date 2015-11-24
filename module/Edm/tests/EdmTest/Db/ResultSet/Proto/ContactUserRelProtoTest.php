<?php

namespace EdmTest\Db\ResultSet\Proto;

use Edm\Db\ResultSet\Proto\ContactUserRelProto;

class ContactUserRelProtoTest extends \PHPUnit_Framework_TestCase {
    public function testGetAllowedKeysForProto () {
        $proto = new ContactUserRelProto();
        $this->assertArraySubset(['screenName', 'email'], $proto->getAllowedKeysForProto());
    }
}

