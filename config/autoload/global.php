<?php

defined ('APP_PATH') ||
    define ('APP_PATH', __DIR__ . '/../../');

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return array(
    'edm-admin-acl' => [
        'resources' => [
            'edm-admin-index' => '',
            'edm-admin-term' => 'edm-admin-index',
            'edm-admin-post' => 'edm-admin-term',
            'edm-admin-term-taxonomy' => 'edm-admin-term',
            'edm-admin-user' => 'edm-admin-index'
        ],
        'roles' => [
            'cms-guest' => null,
            'cms-author' => 'cms-guest',
            'cms-editor' => 'cms-editor',
            'cms-publisher' => 'cms-publisher',
            'cms-admin' => 'cms-admin',
            'cms-super-admin' => 'cms-super-admin'
        ],
        'relationship_map' => [
            'cms-guest' => [
                'allow' => [
                    '*' => 'index',
                    'edm-admin-index' => ['index', 'login', 'logout']
                ]
            ],
            'cms-user' => [
                'allow' => [
                    'edm-admin-post' => ['index', 'create', 'read', 'update', 'delete'],
                    'edm-admin-term-taxonomy' => ['index', 'create', 'read', 'update', 'delete']
                ]
            ],
            'cms-admin' => [
                'allow' => [
                    'edm-admin-user' => ['index', 'create', 'read', 'update', 'delete']
                ]
            ]
        ]
    ],
    'edm-rbac' => [
        
    ],
    'edm-db' => [
        'driver' => 'PDO_Mysql',
        'host' => '127.0.0.1',
        'dbname' => 'some-db-name-here',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter'
            => 'Zend\Db\Adapter\AdapterServiceFactory',
        ]
    ],
);
