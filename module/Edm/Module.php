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
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

//    Zend\Db\ResultSet\ResultSet,
//    Edm\Model\Term,
//    Edm\Model\TermTaxonomy,
//    Edm\Model\TermTaxonomyTable,
//    Edm\Model\TermTable;

class Module implements AutoloaderProviderInterface {

    public function getServiceConfig() {
        return array(
//            'abstract_factories' => array(),
//            'aliases' => array(),
            'invokables' => array(
                'Edm\Db\Table\TermTable'   => 'Edm\Db\Table\TermTable',
                'Edm\Db\Table\TermTaxonomyTable'   => 'Edm\Db\Table\TermTaxonomyTable',
                'Edm\Model\Term'        => 'Edm\Model\Term'
            ),
//            'services' => array(),
//            'shared' => array(),
//            'factories' => array(
//                'Edm\Model\TermTable' => function($sm) {
//                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
//                    $resultSetProto = new ResultSet();
//                    $resultSetProto->setArrayObjectPrototype(new Term());
//                    return new TermTable('terms', $adapter, null, $resultSetProto);
//                },
//                'Edm\Model\TermTaxonomyTable' => function($sm) {
////                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
//                    $resultSetProto = new ResultSet();
//                    $resultSetProto->setArrayObjectPrototype(new TermTaxonomy());
//                    return new TermTaxonomyTable('term_taxonomies', $dbAdapter, null, $resultSetProto);
//                },
//            ),
        );
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
        return include __DIR__ . '/module.config.php';
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
