<?php

namespace Edm\Permissions;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
        return $this->_addRoles($roles)
                    ->_addResources($resources)
                    ->_addPermissions($relationsMap);
    }

    protected function _addRoles(array $roles = null) {
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

    protected function _addResources(array $resources = null) {
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

    protected function _addPermissionsForRole (
            string $roleName, 
            string $allowOrDeny = 'allow', 
            array $resourceAndPermissionsPair = null
    ) {
        foreach ($resourceAndPermissionsPair as $resource => $permissions) {
            $this->{$allowOrDeny}($roleName, $resource, $permissions);
        }
        return $this;
    }
    
    protected function _addPermissions () {
        
    }

}
