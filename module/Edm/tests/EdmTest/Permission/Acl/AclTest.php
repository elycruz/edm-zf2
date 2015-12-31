<?php

declare(strict_types=1);

namespace EdmTest\Permission\Acl;

use Edm\Permissions\Acl\Acl,
    EdmTest\Bootstrap;

class AclTest extends \PHPUnit_Framework_TestCase
{
    public static $config;
    
    public static function setUpBeforeClass() {
        $config = Bootstrap::getAutoloadConfig()['edm-admin-acl'];
        self::$config = $config;
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
    
    public function testAddRoles () {
        $acl = new Acl();
        $roles = ['role1' => null, 'role2' => 'role1', 'role3' => 'role2', 'role4' => 'role3'];
        $acl->addRoles($roles);
        $expectedRoles = array_keys($roles);
        foreach ($expectedRoles as $role) {
            $this->assertTrue($acl->hasRole($role), 'It should have `role` "' . $role . '".');
        }
    }
    
    public function testAddResources () {
        $acl = new Acl();
        $resources = ['resource1' => null, 'resource2' => 'resource1', 'resource3' => 'resource2', 'resource4' => 'resource3'];
        $acl->addResources($resources);
        $expectedResources = array_keys($resources);
        foreach ($expectedResources as $resource) {
            $this->assertTrue($acl->hasResource($resource), 'It should have `resource` "' . $resource . '".');
        }
    }
    
    public function testAddAclDefinitionForRole () {
        $roleAclDefinition = [
            'roles' => [
                'guest' => null,
                'user' => 'guest',
                'admin' => 'user'
            ],
            'resources' => [
                'index' => null,
                'post' => 'index',
                'contact' => null
            ],
            'relationships' => [
                'guest' => [
                    'allow' => [
                        'index' => '*',
                        'post' => '*'
                    ],
                    'deny' => [
                        'conact' => '*'
                    ]
                ],
            ]
        ];
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
    
    public function testSetsAclRulesViaConstruction () {
        $acl = $this->acl();
        $config = $this->getConfig();
        $resources = array_keys($config['resources']);
        foreach ($resources as $resource) {
            $hasResource = $acl->hasResource($resource);
            $this->assertTrue($hasResource, 'Should have resource "' . $resource . '".');
        }
    }
    
}