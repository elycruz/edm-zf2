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
    
    public function aclConfigProvider () {
        return [
            [[
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
                'acl_definition' => [
                    'guest' => [
                        'allow' => [
                            'index' => null,
                            'post' => null
                        ],
                        'deny' => [
                            'contact' => '*'
                        ]
                    ],
                ]
            ]]
        ];
    }
    
    public function acl () {
        $config = Bootstrap::getAutoloadConfig()['edm-admin-acl'];
        return new Acl($config, 'roles', 'resources', 'relationships_map');
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
    
    /**
     * @dataProvider aclConfigProvider
     * @param array $aclConfig
     */
    public function testAddAclDefinitionForRole (array $aclConfig) {        
        // Create `acl`
        $acl = new Acl();
        
        // Add role acl definition
        $result = $acl->addRoles($aclConfig['roles'])
            ->addResources($aclConfig['resources'])
            ->addAclDefinitionForRole('guest', $aclConfig['acl_definition']['guest']);
        
        // Assert `acl` is returned from adding acl role definition operation
        $this->assertEquals($acl, $result);
        
        // Make some assertions
        // Assert 'guest' has access to 'index' resource
        $this->assertTrue($acl->isAllowed('guest', 'index'), 
                '"guest" role should be allowed to access resource "index".');
        
        // Assert guest has access to 'post' resource
        $this->assertTrue($acl->isAllowed('guest', 'post'), 
                '"guest" role should be allowed to access resource "post".');
        
        // Assert guest is denied access to 'contact' resource
        $this->assertFalse($acl->isAllowed('guest', 'contact'),
                '"guest" role should not be allowed to access resource "contact".');
    }
    
    /**
     * @dataProvider aclConfigProvider
     * @param array $aclConfig
     */
    public function testAddAclDefinition (array $aclConfig) {
        $acl = new Acl();
        $result = $acl->addRoles($aclConfig['roles'])
                ->addResources($aclConfig['resources'])
                ->addAclDefinition($aclConfig['acl_definition']);
                
        // Assert `acl` is returned from adding acl role definition operation
        $this->assertEquals($acl, $result);
        
        // Make some assertions
        // Assert 'guest' has access to 'index' resource
        $this->assertTrue($acl->isAllowed('guest', 'index'), 
                '"guest" role should be allowed to access resource "index".');
        
        // Assert guest has access to 'post' resource
        $this->assertTrue($acl->isAllowed('guest', 'post'), 
                '"guest" role should be allowed to access resource "post".');
        
        // Assert guest is denied access to 'contact' resource
        $this->assertFalse($acl->isAllowed('guest', 'contact'),
                '"guest" role should not be allowed to access resource "contact".');
    }
    
    /**
     * @dataProvider aclConfigProvider
     * @param array $aclConfig
     */
    public function testAddPermissionsForUser (array $aclConfig) {
        $acl = new Acl();
        $result = $acl->addRoles($aclConfig['roles'])
                ->addResources($aclConfig['resources'])
                ->addPermissionsForRole('guest', 'allow', $aclConfig['acl_definition']['guest']['allow'])
                ->addPermissionsForRole('guest', 'deny', $aclConfig['acl_definition']['guest']['deny']);
                
        // Assert `acl` is returned from adding acl role definition operation
        $this->assertEquals($acl, $result);
        
        // Make some assertions
        // Assert 'guest' has access to 'index' resource
        $this->assertTrue($acl->isAllowed('guest', 'index'), 
                '"guest" role should be allowed to access resource "index".');
        
        // Assert guest has access to 'post' resource
        $this->assertTrue($acl->isAllowed('guest', 'post'), 
                '"guest" role should be allowed to access resource "post".');
        
        // Assert guest is denied access to 'contact' resource
        $this->assertFalse($acl->isAllowed('guest', 'contact'),
                '"guest" role should not be allowed to access resource "contact".');
    }
    
    /**
     * @dataProvider aclConfigProvider
     * @param array $aclConfig
     */
    public function testPopulateFromArrayMap (array $aclConfig) {
        $acl = new Acl();
        $result = $acl->populate($aclConfig['roles'], $aclConfig['resources'], $aclConfig['acl_definition']);
                
        // Assert `acl` is returned from adding acl role definition operation
        $this->assertEquals($acl, $result);
        
        // Make some assertions
        // Assert 'guest' has access to 'index' resource
        $this->assertTrue($acl->isAllowed('guest', 'index'), 
                '"guest" role should be allowed to access resource "index".');
        
        // Assert guest has access to 'post' resource
        $this->assertTrue($acl->isAllowed('guest', 'post'), 
                '"guest" role should be allowed to access resource "post".');
        
        // Assert guest is denied access to 'contact' resource
        $this->assertFalse($acl->isAllowed('guest', 'contact'),
                '"guest" role should not be allowed to access resource "contact".');
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