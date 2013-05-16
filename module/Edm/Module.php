<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Edm for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Edm;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\Mvc\ModuleRouteListener,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature,
    Zend\ModuleManager\Feature\FormElementProviderInterface;

class Module implements 
    AutoloaderProviderInterface, 
        FormElementProviderInterface {

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

    public function getFormElementConfig () {
        return include __DIR__ . '/configs/form.element.config.php';
    }
    
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

    public function registerGlobalDbAdapter($e) {
        $locator = $e->getTarget()->getServiceManager();
        $adapter = $locator->get('Zend\Db\Adapter\Adapter');
        GlobalAdapterFeature::setStaticAdapter($adapter);
    }

    public function onBootstrap($e) {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();

        // Register a "render" event, at high priority (so it executes prior
        // to the view attempting to render)
        $this->registerGlobalDbAdapter($e);
        $eventManager->attach('render', array($this, 'registerJsonStrategy'), 100);

        $moduleRouteListener->attach($eventManager);
    }

}
