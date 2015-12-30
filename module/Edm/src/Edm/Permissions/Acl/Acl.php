<?php

declare(strict_types=1);

namespace Edm\Permissions;

use Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Role\GenericRole;

class Acl extends ZendAcl {

    public function __construct(array $array_map = null, 
            string $rolesKey = 'roles',
            string $resourcesKey = 'resources',
            string $relationsMapKey = 'relationships_map') {
        $this->populateFromArrayMap(
            is_array($array_map[$rolesKey]) ? $array_map[$rolesKey] : null,
            is_array($array_map[$resourcesKey]) ? $array_map[$resourcesKey] : null,
            is_array($array_map[$relationsMapKey]) ? $array_map[$relationsMapKey] : null
        );
    }

    public function populateFromArrayMap(
            array $roles = null, 
            array $resources = null, 
            array $relationsMap = null
    ) {
        return $this->addRoles($roles)
                    ->addResources($resources)
                    ->addPermissionsForRoleAndAclDefinitionMap($relationsMap);
    }

    public function addRoles(array $roles = null) {
        if (!isset($roles)) {
            return $this;
        }
        foreach ($roles as $name => $parents) {
            if ($this->hasRole($name)) {
                continue;
            }
            $this->addRole(new GenericRole($name), 
                    !isset($parents) ? [] : $parents);
        }
        return $this;
    }

    public function addResources(array $resources = null) {
        if (!isset($resources)) {
            return $this;
        }
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
            array $resourceAndPermissionsPair = null
    ) {
        // Throw an exception if `$allowOrDeny` is not equal to 'allow' or 'deny'
        if ($allowOrDeny !== 'allow' || $allowOrDeny !== 'deny') {
            throw new Exception ('`' . __CLASS__ . '->' . __FUNCTION__ 
                    . '` only allows a value of "allow" or "deny" for it\'s '
                    . '`$allowOrDeny` parameter.  Value recieved "' . $allowOrDeny . '".');
        }
        foreach ($resourceAndPermissionsPair as $resource => $permissions) {
            $this->{$allowOrDeny}($roleName, $resource, $permissions);
        }
        return $this;
    }
    
    protected function _processRoleAclDefinition (string $roleName, array $roleDefinition) {
        foreach ($roleDefinition as $allowOrDeny => $resourceAndPermissionsMap) {
            $this->_addPermissionsForRole($roleName, $allowOrDeny, $resourceAndPermissionsMap);
        }
        return $this;
    }
    
    public function addPermissionsForRoleAndAclDefinitionMap (array $rolesAndPermissionsMap) {
        foreach ($rolesAndPermissionsMap as $role => $definition) {
            $this->_processRoleAclDefinition($role, $definition);
        }
        return $this;
    }
    
}
