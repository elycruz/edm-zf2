<?php

namespace EdmSession;

use Zend\Session\Config\SessionConfig;
use Zend\Session\SaveHandler\DbTableGateway;
use Zend\Session\SaveHandler\DbTableGatewayOptions;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\Db\TableGateway\TableGateway;

/**
 * Description of Module
 *
 * @author ElyDeLaCruz
 */
class Module {

    public function onBootstrap(\Zend\EventManager\EventInterface $e) {
        $sm = $e->getApplication()->getServiceManager();
        $config = $sm->get('session_config');
        $storage = null;
        if ($sm->has('session_storage', false)) {
            $storage = $sm->get('session_storage');
        }
        $saveHandler = null;
        if ($sm->has('session_save_handler', false)) {
            $saveHandler = $sm->get('session_save_handler');
        }
        $sessionManager = new SessionManager($config, $storage, $saveHandler);
        Container::setDefaultManager($sessionManager);
        
        // Start session
        $sessionManager->start();
       
        // Start own container
        $container = new Container('edmSession');
        
        // Track visit count
        $container->visitCount = !isset($container->visitCount) ? 
            0 : $container->visitCount += 1;
        
        if (empty($container->created)) {
            $container->created = true;
        }
        
        // Make sure we created our session if not regenrate id, create
        // edm session namespace and set created to true
        if (!$container->created) {
            $sessionManager->regenerateId();
            $container = new Container('edmSession');
            $container->created = true;
            $container->visitCount = 0;
        } 
        else {
            $container->visitCount += 1;
        }

        // Check to see if we have a user_agent variable in our global namespace
        if (!isset($container->user_agent)) {
            // Store user_agent hash in our session and in a cookie
            $hash = $container->user_agent = $this->genUserAgentHash();
            setcookie('user_agent', $hash, 0);
        } 
        else if ($container->user_agent != $this->genUserAgentHash()
                 || (isset($_COOKIE['user_agent']) &&
                $_COOKIE['user_agent'] != $container->user_agent)) {

            // Destroy session
            // Regenerate id
            $sessionManager->regenerateId();
            $container = new Container('edmSession');
            $container->created = true;

            // @todo if count exceeds sessionVisitLimit destroy session and 
            // make entry into db.  If this occurs 3 times for the same 
            // visitor ban their ip address till further notice
            $container->visitCount = 0;

            // Set user agent
            $hash = $container->user_agent = $this->genUserAgentHash();
            setcookie('user_agent', $hash, 0);

            // Throw exception
            throw new \Exception(
                    '<p>Your session has ended.  Please visit the' .
                    'link below to start a new session.</p><br />' .
                    '<a href="/">Site root</a>'
            );
        }
    }
    
    public function genUserAgentHash () {
        return md5(SALT . $_SERVER['HTTP_USER_AGENT'] . PEPPER);
    }
    
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'session_config' => function ($sm) {
                    $config = $sm->get('Config');
                    $sessionConfig = new SessionConfig();
                    if (isset($config['session'])) {
                        $sessionConfig->setOptions($config['session']);
                    }
                    return $sessionConfig;
                },
                'session_save_handler' => function ($sm) {
                    $options = new DbTableGatewayOptions();
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $gateway = new TableGateway('sessions', $adapter);
                    $handler = new DbTableGateway($gateway, $options);
                    return $handler;
                }
            ),
        );
    }

    public function getConfig() {
        return array(
            'session' => array(
                'name' => 'EDM_SESSION',
                'gc_maxlifetime' => 14400
            ),
        );
    }

}