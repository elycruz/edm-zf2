<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Edm for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace EdmDefault;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\Mvc\ModuleRouteListener;

class Module implements 
    AutoloaderProviderInterface  {

//    public function getServiceConfig() {
//        return array(
//            'abstract_factories' => array(),
//            'aliases' => array(),
//            'invokables' => array(),
//            'services' => array(),
//            'shared' => array(),
//            'factories' => array(),
//        );
//    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one 
                    // level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' .
                    str_replace('\\', '/', __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/configs/module.config.php';
    }

    public function registerJsonStrategy($e) {
        $app = $e->getTarget();
        $locator = $app->getServiceManager();
        $view = $locator->get('Zend\View\View');
        $jsonStrategy = $locator->get('ViewJsonStrategy');
        // Attach strategy, which is a listener aggregate, at high priority
        $view->getEventManager()->attach($jsonStrategy, 100);
    }

    public function onBootstrap($e) {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();

        // Register a "render" event, at high priority (so it executes prior
        // to the view attempting to render)
        $eventManager->attach('render', array($this, 'registerJsonStrategy'), 100);
        $eventManager->attach('dispatch', array($this, 'setTemplate'), -100);
        $moduleRouteListener->attach($eventManager);
    }
    
    public function setTemplate ($e) {
        $matches    = $e->getRouteMatch();
        $controller = $matches->getParam('controller');
        if (false === strpos($controller, __NAMESPACE__)) {
            // not a controller from this module
            return;
        }
        // Set the layout template
        $viewModel = $e->getViewModel();
        $viewModel->setTemplate('layout/edm-default-ui');
    }

}
