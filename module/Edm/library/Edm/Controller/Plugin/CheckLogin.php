<?php
/**
 * Checks if user authorized to view the requested resource/privilege
 * @author ElyDeLaCruz
 */
class Edm_Controller_Plugin_CheckLogin
    extends Zend_Controller_Plugin_Abstract
{
    /**
     * Edm Acl
     * @var Edm_Acl
     */
    protected $_acl;
    
    /**
     * Check for our user->token
     * @param Zend_Controller_Request_Abstract $request
     */
    public function  preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Call parent's predispatch method
        parent::preDispatch($request);

        // Get current module
        $currModule = $request->getModuleName();

        // Per module acl
        if ($currModule == 'edm-admin' || 
            $currModule == 'edm-admin-rest' || 
            $currModule == 'edm-install') {
            $config = new Zend_Config_Ini(
                    APPLICATION_PATH .'/configs/edm-admin/acl.ini',
                    APPLICATION_ENV);
        }
        else if ($currModule == 'edm-default') {
            $config = new Zend_Config_Ini(
                    APPLICATION_PATH .'/configs/edm-default/acl.ini',
                    APPLICATION_ENV);
        }
        else if ($currModule == 'edm-file-browser') {
            $config = new Zend_Config_Ini(
                    APPLICATION_PATH .'/configs/edm-file-browser/acl.ini',
                    APPLICATION_ENV);
        }

        // Get Acl
        $acl = new Edm_Acl($config);
        Zend_Registry::set('edm-acl', $acl);
        
        // Get Auth
        $auth = Zend_Auth::getInstance();
        
        // Get Response
        $response = $this->getResponse();

        // Check if user is logged in and if so 
        // set a logged in flag
        if($auth->hasIdentity())
        {
            $role = $auth->getIdentity()->role;
            $response->insert('loggedIn', true);
        }
        else {
            $role = 'guest';
            $response->insert('loggedIn', false);
        }

        // Set resource and privilege
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $module = $request->getModuleName();
        $resource = $controller;
        $privilege = $action;
        
        // Make sure resource is available
        if( !$acl->has($resource)){
            return $request->setModuleName($currModule)
                    ->setControllerName('error')
                    ->setActionName('resource-not-found')
                    ->setDispatched(false);
        }

        // Make sure this user is allowed to view the requested resources
        if(!$acl->isAllowed($role, $resource, $privilege) &&
                $acl->has($resource)) {
            return $request->setModuleName($currModule)
                    ->setControllerName('error')
                    ->setActionName('not-authorized')
                    ->setDispatched(false);
        }
    }
    }