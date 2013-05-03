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
    Zend\Session\Container;

class Module implements AutoloaderProviderInterface {

    /**
     * Acl object
     * @var EdmAccessGateway\Permissions\Acl\Acl
     */
    protected $acl;

    public function onBootstrap(MvcEvent $e) {
        $app = $e->getApplication();
        $eventMngr = $app->getEventManager();
        $eventMngr->attach('route', $this->onRoute, 100);
        $this->acl = $app->getServiceManager()
                ->get('EdmAccessGateway\Permissions\Acl\Acl');
    }

    public function onRoute($e) {
        // Get route match
        $routeMatch = $e->getRouteMatch();

        // Get module name (if any)
        $module = $routeMatch->getParam('module');

        // Get controller name
        $controller = $routeMatch->getParam('controller');

        // Get action name
        $action = $routeMatch->getParam('action');

        // Get Acl
        $acl = $this->acl->setConfig(new Config(include('configs/edm-acl-config.php')));
        
        // Get session container 
        $session = new Container('edmSession');
        
        // Get current user
        $user = $session->user;
        
        // Get role
        if (empty($user)) {
            $role = 'guest';
        }
        else {
            $role = $user->role;
        }
        
        // Restrict access
        
        
        // Redirect if necessary
        
        // Etc.
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
