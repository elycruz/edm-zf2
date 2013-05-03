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
                if (empty($parents)) {
                    $parents = array();
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
        foreach ($resources as $resource) {

            // For each resource privilege (action)
            foreach ($resource as $privileges) {


                // Resolve if all access
                if ('all' === $resource) {
                    $resource = null;
                }
                // Else add resource
                else if (!$this->has($resource)) {
                    if (is_string($privileges)) {
                        $parentResource = $privileges;
                        $this->add(new Resource($resource), $parentResource);
                        continue;
                    }
                    $this->add(new Resource($resource));
                }

                // For each $role => $permission in $privileges
                foreach ($privileges as $privilege => $roles) {
                    // If privilege is all 
                    if ($privilege === 'all') {
                        $privilege = null;
                    }

                    // Loop through roles and get their permissions
                    foreach ($roles as $role => $permission) {
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
    }

    /**
     * Inject the acl configuration into our acl object
     * @param \Zend\Config\Config $config
     * @return \EdmAccessGateway\EdmAccessGatewayAcl
     */
    public function setConfig(Config $config) {
        $this->addRoles($config->roles);
        $this->addResourcesAndPerms(
                $config->resources_and_permissions);
        return $this;
    }

}
