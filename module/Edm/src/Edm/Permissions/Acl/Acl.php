<?php

declare(strict_types=1);

namespace Edm\Permissions\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Role\GenericRole;

class Acl extends ZendAcl {

    public function __construct(array $array_map = null, 
            string $rolesKey = 'roles',
            string $resourcesKey = 'resources',
            string $aclDefinitionKey = 'acl_definition') {
        $this->populate (
            is_array($array_map[$rolesKey]) ? $array_map[$rolesKey] : null,
            is_array($array_map[$resourcesKey]) ? $array_map[$resourcesKey] : null,
            is_array($array_map[$aclDefinitionKey]) ? $array_map[$aclDefinitionKey] : null
        );
    }

    public function populate (
            array $roles = null, 
            array $resources = null, 
            array $aclDefinition = null
    ) {
        if (isset($roles)) {
            $this->addRoles($roles);
        }
        if (isset($resources)) {
            $this->addResources($resources);
        }
        if (isset($aclDefinition)) {
            $this->addAclDefinition($aclDefinition);
        }
        return $this;
    }

    public function addRoles(array $roles) {
        foreach ($roles as $name => $parents) {
            if ($this->hasRole($name)) {
                continue;
            }
            $this->addRole(new GenericRole($name), 
                    !isset($parents) ? [] : $parents);
        }
        return $this;
    }

    public function addResources(array $resources) {
        foreach ($resources as $resource => $parent) {
            if ($this->hasResource($resource)) {
                continue;
            }
            $this->addResource($resource,
                    !isset($parent) ? null : $parent);
        }
        return $this;
    }

    public function addPermissionsForRole (
            string $roleName, 
            string $allowOrDeny = 'allow', 
            array $resourceAndPermissionPairs = null
    ) {
        // Throw an exception if `$allowOrDeny` is not equal to 'allow' or 'deny'
        if ($allowOrDeny !== 'allow' && $allowOrDeny !== 'deny') {
            throw new \Exception ('`' . __CLASS__ . '->' . __FUNCTION__ 
                    . '` only allows a value of "allow" or "deny" for it\'s '
                    . '`$allowOrDeny` parameter.  Value recieved "' . $allowOrDeny . '".');
        }
        foreach ($resourceAndPermissionPairs as $resource => $permissions) {
            if ($resource === '*' || $resource === 'all') {
                $resource = null;
            }
            $this->{$allowOrDeny}($roleName, $resource, $permissions);
        }
        return $this;
    }
    
    public function addAclDefinitionForRole (string $roleName, array $roleDefinition) {
        foreach ($roleDefinition as $allowOrDeny => $resourceAndPermissionsMap) {
            $this->addPermissionsForRole($roleName, $allowOrDeny, $resourceAndPermissionsMap);
        }
        return $this;
    }
    
    public function addAclDefinition (array $aclDefinition) {
        foreach ($aclDefinition as $role => $definition) {
            $this->addAclDefinitionForRole($role, $definition);
        }
        return $this;
    }
    
}
