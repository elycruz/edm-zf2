<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendIndexApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace EdmAccessGateway;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\Config\Config,
    Zend\Mvc\MvcEvent,
    Zend\Permissions\Acl\Acl,
    Zend\Navigation\Navigation;

class Module implements AutoloaderProviderInterface {

    /**
     * Acl object
     * @var EdmAccessGateway\Permissions\Acl\Acl
     */
    protected $acl;
    
    protected $user_role;

    public function getConfig() {
        $config = include __DIR__ . '/configs/module.config.php';
        $config['acl'] = include __DIR__ . '/configs/edm-acl-config.php';
        return $config;
    }

    /**
     * Service locator
     * @var Zend\ServiceManager\ServiceLocator
     */
    protected $serviceLocator;

    public function onBootstrap(MvcEvent $e) {
        $app = $e->getApplication();
        
        $this->serviceLocator = $app->getServiceManager();
        
        $eventMngr = $app->getEventManager();
        $eventMngr->attach('route', array($this, 'onRoute'), -100);
        $eventMngr->attach('route', array($this, 'setNavigation'), -1000);

        $this->acl = $app->getServiceManager()
                ->get('EdmAccessGateway\Permissions\Acl\Acl');

    }

    public function onRoute(MvcEvent $e) {        
        // Get route match
        $routeMatch = $e->getRouteMatch();
        
        // Make router available via the service locator
//        $this->serviceLocator->setService('edm-router', $e->getRouter());

        // Get module name (if any)
        $module = $routeMatch->getParam('module');

        // Get resource/controller name
        $resource = $routeMatch->getParam('__CONTROLLER__');

        // Get privilege/action name
        $privilege = $routeMatch->getParam('action');

        // Get Acl
        $aclSource = include(__DIR__ . '/configs/edm-acl-config.php');
        $acl = $this->acl->setConfig(new Config($aclSource));
        
        // Make acl accessible from outside
        $e->getApplication()->getServiceManager()->setService('edm-acl', $acl);

        // Get auth service
        $authService = $this->serviceLocator->get('Zend\Authentication\AuthService');

        // If has identity set user else use default user role
        if ($authService->hasIdentity()) {
            $role = $authService->getIdentity()->role;
        } else {
            $role = 'guest';
        }
        
        // Set role (used later by set navigation handler)
        $this->user_role = $role;

        // Restrict access
        // var_dump('user role is "' . $role . '" <br /> '. $resource);
        if (preg_match('/\\+/', $resource) > -1) {
            $resourceParts = explode('\\', strtolower($resource));
            $resource = $resourceParts[count($resourceParts) - 1];
        }

        // Make sure we log users out when they visit edm-admin pages
        if ($role === 'user' && $module === 'edm-admin' && 
                $resource !== 'index') {
            return $routeMatch
                            ->setParam('controller', 'Edm\\Controller\\Index')
                            ->setParam('action', 'logout')
                            ->setParam('dispatched', false);
        }
        
        // Redirect un-authorized users
        if (($acl->hasResource($resource) && 
                !$acl->isAllowed($role, $resource, $privilege))) {
            return $routeMatch
                            ->setParam('controller', 'Edm\\Controller\\Index')
                            ->setParam('action', 'index')
                            ->setParam('dispatched', false);
        }
    }
    
        
    public function setNavigation ($e) {  
        
        $serviceManager = $e->getApplication()->getServiceManager();
        
        // Get nav config
        $config = new Config(include 'configs/edm-navigation-config.php');
        
        // Set default nav
        $nav = new Navigation($config['default']);
        
        $serviceManager->setService('edm-navigation', $nav);
    }
    
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

}
