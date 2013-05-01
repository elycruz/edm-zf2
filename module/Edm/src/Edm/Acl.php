<?php
namespace Edm;

use Zend\Config\Config,
Zend\Permissions\Acl as ZendAcl;

class Acl extends ZendAcl
{

    public function __construct(Config $config)
    {
       $roles = $config->acl->roles;
       $resources = $config->acl->resources;
       $this->addRoles($roles);
       $this->addResources($resources);
    }

    private function addRoles($roles)
    {
        foreach($roles as $name => $parents){
            if(!$this->hasRole($name)) {
                if(empty($parents)){
                    $parents = array();
                } else {
                    $parents = explode(',', $parents);
                }

                $this->addRole(new ZendAclRole($name), $parents);
            }
        }
    }

    private function addResources($resources)
    {
        foreach($resources as $permissions => $controllers){
            foreach($controllers as $controller => $actions){

                if('all' == $controller){
                    $controller = null;
                } else {
                    if(!$this->has($controller)){
                        $this->add(new ZendAclResource($controller));
                    }
                }

                foreach($actions as $action => $role){
                    if($action == 'all') {
                        $action = null;
                    }
                    if($permissions == 'allow'){
                        $this->allow($role, $controller, $action);
                    }
                    if($permissions == 'deny'){
                        $this->deny($role, $controller, $action);
                    }
                }
            }
        }
    }


}
