<?php

// Module Configuration
return array(
    'service_manager' => array(
        'invokables' => array(
            'EdmAccessGateway\Permissions\Acl\Acl' =>
                'EdmAccessGateway\Permissions\Acl\Acl'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'error' => 'EdmAccessGateway\Controller\ErrorController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'EdmAccessGateway' => __DIR__ . '/../view-scripts',
        )
    ),
);
