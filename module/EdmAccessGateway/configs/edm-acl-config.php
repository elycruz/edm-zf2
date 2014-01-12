<?php
/**
 * This is the access control list configuration for our appliation.
 * Note it seems light weight but don't forget that we have an rbac
 * implementation to give us finer grained control from the areas where
 * we will need it (@see Zend\Permissions\Rbac).
 */
return array(
    
    'roles' => array(
        'guest'         => 'none',
        'user'          => 'guest',
        'cms-user'      => 'user',
        'cms-author'    => 'cms-user',
        'cms-editor'    => 'cms-author',
        'cms-publisher' => 'cms-editor',
        'cms-admin'     => 'cms-publisher',
        'cms-manager'   => 'cms-admin',
        'cms-super-admin' => 'cms-manager'
    ),

    'resources_and_privileges' => array(
        
        'term' => array(
            'index' => array(
                'cms-author' => 'allow'),
            'create' => array(
                'cms-editor' => 'allow'),
            'update' => array(
                'cms-editor' => 'allow'),
            'delete' => array(
                'cms-publisher' => 'allow')
        ),
        
        'term-taxonomy' => 'term',
        
        'view-module' => 'term',
        
        'menu' => 'view-module',
        
        'user' => array(
            'index' => array(
                'cms-admin' => 'allow'),
            'create' => array(
                'cms-admin' => 'allow'),
            'update' => array(
                'cms-admin' => 'allow'),
            'delete' => array(
                'cms-manager' => 'allow')
        ),
        
        'post' => array(
            'index' => array(
                'cms-user' => 'allow'),
            'create' => array(
                'cms-author' => 'allow'),
            'update' => array(
                'cms-author' => 'allow'),
            'delete' => array(
                'cms-author' => 'allow'),
            'flash-messages-to-json' => array(
                'cms-author' => 'allow'),
        ),

        'error' => array(
            'all' => array('guest' => 'allow')
        ),
        
        'ajax-ui' => array(
            'all' => array('cms-user' => 'allow')
        ),
        
        'index' => array(
            // Redirect to user/login allows for some interesting
            // Security possibilites
            'index'     => array(
                'guest' => 'allow'),
            // Redirect to user/logout
            'logout'    => array(
                'guest' => 'allow'),
            // Redirect to user/login
            'login'     => array(
                'guest' => 'allow')
        ),
        
        'dashboard' => array(
            'all' => array(
                'cms-user' => 'allow')
        ),
    )
);