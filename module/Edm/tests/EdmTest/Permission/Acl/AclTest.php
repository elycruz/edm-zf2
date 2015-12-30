<?php

declare(strict_types=1);

namespace EdmTest\Permission\Acl;

use \Edm\Permissions\Acl\Acl,
    EdmTest\Bootstrap;

class TermProtoTest extends \PHPUnit_Framework_TestCase
{
    
//    public static function setUpBeforeClass () {
//        
//    }
    
    public function acl () {
        $config = Bootstrap::getAutoloadConfig()['edm-admin-acl'];
        return new Acl($config);
    }
    
    public function testAclType () {
        $acl = $this->acl();
        $this->assertInstanceOf('\Edm\Permissions\Acl\Acl', $acl);
    }
    
}