<?php

declare(strict_types=1);

namespace EdmTest\Permission\Acl;

use Edm\Permissions\Acl\Acl,
    EdmTest\Bootstrap;

class TermProtoTest extends \PHPUnit_Framework_TestCase
{
    public static $expectectRoles;
    
    public static $expectectResources;
    
    public static $config;
    
    public static function setUpBeforeClass() {
        $config = Bootstrap::getAutoloadConfig()['edm-admin-acl'];
        self::$config = $config;
        static::$expectectResources = $config['resources'];
        static::$expectectRoles = $config['roles'];
    }
    
    public function getConfig() {
        return self::$config;
    }
    
    public function acl () {
        $config = Bootstrap::getAutoloadConfig()['edm-admin-acl'];
        return new Acl($config);
    }
    
    public function testConstructorType () {
        $acl = $this->acl();
        $this->assertInstanceOf('Edm\Permissions\Acl\Acl', $acl,
                'Should be of expected class type `Edm\Permissions\Acl\Acl`.');
    }
    
    public function testConstructWithOutConfig () {
        $acl = new Acl();
        $this->assertInstanceOf('Edm\Permissions\Acl\Acl', $acl,
            'Should be of expected class type `Edm\Permissions\Acl\Acl`.');
    }
    
    public function testPopulatesRolesViaConstruction () {
        $acl = $this->acl();
        $config = $this->getConfig();
        $roles = array_keys($config['roles']);
        foreach ($roles as $role) {
            $hasRole = $acl->hasRole($role);
            $this->assertTrue($hasRole, 'Should have role "' . $role . '".');
        }
    } 
    
    public function testPopulatesResourcesViaConstruction () {
        $acl = $this->acl();
        $config = $this->getConfig();
        $resources = array_keys($config['resources']);
        foreach ($resources as $resource) {
            $hasResource = $acl->hasResource($resource);
            $this->assertTrue($hasResource, 'Should have resource "' . $resource . '".');
        }
    }
    
}