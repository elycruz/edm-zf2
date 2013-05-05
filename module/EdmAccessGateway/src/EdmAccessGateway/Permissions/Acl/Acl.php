<?php

namespace EdmAccessGateway\Permissions\Acl;

use Zend\Config\Config,
    Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Resource\GenericResource as Resource,
    Zend\Permissions\Acl\Role\GenericRole as Role;

/**
 * A quick and easy way to implement an access control list.
 */
class Acl extends ZendAcl {

    /**
     * Constructs our Acl
     * @param \Zend\Config\Config $config
     */
    public function __construct(Config $config = null) {
        if ($config !== null) {
            $this->setConfig($config);
        }
    }

    /**
     * Adds our roles to our acl
     * @param mixed array|Config $roles
     */
    protected function addRoles($roles) {
        foreach ($roles as $name => $parents) {
            if (!$this->hasRole($name)) {
                if (empty($parents) || $parents === 'none') {
                    $parents = null;
                }
                $this->addRole(new Role($name), $parents);
            }
        }
    }

    /**
     * Adds the resources and permissions to our access control list
     * @param mixed array|Config $resources
     * @throws \Exception
     */
    protected function addResourcesAndPerms($resources) {
        // For each resource (controller)
        foreach ($resources as $resource => $resourceVals) {

            // ------------------------------------------------------
            // Set Resources
            // ------------------------------------------------------
            // We allow the resource to set either 1) a parent resource
            // 2) an array of actions/privileges with
            // their role => permission key value
            // Add resource
            if (!$this->hasResource($resource)) {
                if (is_string($resourceVals)) {
                    $parentResource = $resourceVals;
                    $this->addResource(new Resource($resource), $parentResource);
                    continue;
                }
                $this->addResource(new Resource($resource));
            }

            // For each resource privilege (action)
            foreach ($resourceVals as $privilege => $privilegeVals) {


                foreach ($privilegeVals as $role => $permission) {
//                    var_dump($resource . ' > ' . $privilege . ' > ' . $permission . ' > ' . $role . '<br />');
                    // If privilege is equal to all then we pass null when setting permissions 
                    // to allow all
                    if ($privilege === 'all') {
                        $privilege = null;
                    }

                    // ------------------------------------------------------
                    // Set Permissions
                    // ------------------------------------------------------
                    // If allow
                    if ($permission === 'allow') {
                        $this->allow($role, $resource, $privilege);
                    }
                    // If deny
                    else if ($permission === 'deny') {
                        $this->deny($role, $resource, $privilege);
                    }
                    // Else throw exception
                    else {
                        throw new \Exception(__CLASS__ . '->'
                        . __FUNCTION__ . ' expects permission ' .
                        'settings of either "allow" ' .
                        'or "deny".  Value : ' . $permission . ' ' .
                        'is not accepted.');
                    }
                }
            }
        }
    }

    /**
     * Inject the acl configuration into our acl object
     * @param \Zend\Config\Config $config
     * @return \EdmAccessGateway\EdmAccessGatewayAcl
     */
    public function setConfig(Config $config) {
        $roles = $config->roles;
        $resources = $config->resources_and_privileges;

//        var_dump($resources->toArray());
        $this->addRoles($roles->toArray());
        $this->addResourcesAndPerms($resources->toArray());

        return $this;
    }

}
