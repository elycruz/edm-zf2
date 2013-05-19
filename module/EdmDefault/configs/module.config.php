<?php

// @todo update forms to use the init function for initializing 
// so that we can have access to the service manager

//-----------------------------------------------------------------------------
// Defines
//-----------------------------------------------------------------------------

// Module Configuration
return array(
    'service_manager' => array(
        'invokables' => array()
    ),
    'controllers' => array(
        'invokables' => array(
            'EdmDefault\Controller\Index' => 'EdmDefault\Controller\IndexController',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array()
    ),
    'router' => array(
        'routes' => array(
            'edm-default-default' => array(
                'type' => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/',
                    'defaults' => array(
                        // Set for Edm Access Gateway control
                        'module' => 'edm-default',
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'EdmDefault\Controller',
                        'controller' => 'index',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'EdmDefault' => __DIR__ . '/../view-scripts',
        ),
        'template_map' => array(
            'layout/edm-default-ui'     => APP_PATH .'/public/module-templates/edm-default-ui/index.phtml',
        )
    ),
);
