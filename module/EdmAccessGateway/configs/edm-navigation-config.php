<?php

/*
 * Edm Admin Navigation config
 * @type array
 */
return array(
    'default' => array(
        array(
            'label' => 'Content',
            'uri' => '/edm-admin/content',
            'pages' => array(
                array(
                    'label' => 'Pages',
                    'route' => 'paginator',
                    'resource' => 'page',
                    'privilege' => 'cms-author',
                    'controller' => 'post',
                    'action' => 'index',
                    'pages' => array(
                        array(
                            'label' => 'Page Create',
                            'route' => 'edm-admin-default',
                            'privilege' => 'cms-author',
                            'resource' => 'create'
                        )
                    )
                ),
                array(
                    'label' => 'Posts',
                    'route' => 'paginator',
                    'resource' => 'post',
                    'privilege' => 'cms-author',
                    'controller' => 'post',
                    'action' => 'index',
                    'pages' => array(
                        array(
                            'label' => 'Post Create',
                            'route' => 'edm-admin-default',
                            'privilege' => 'cms-author',
                            'resource' => 'create'
                        )
                    )
                ),
                array(
                    'label' => 'View Modules',
                    'route' => 'paginator',
                    'resource' => 'view-module',
                    'privilege' => 'cms-author',
                    'controller' => 'view-module',
                    'action' => 'index',
                    'pages' => array(
                        array(
                            'label' => 'View Module Create',
                            'route' => 'edm-admin-default',
                            'privilege' => 'cms-author',
                            'resource' => 'create'
                        ),
                        array(
                            'label' => 'Menu View Modules',
                            'route' => 'edm-admin-default',
                            'privilege' => 'cms-author',
                            'resource' => 'index/view-module/menu'
                        ),
                        array(
                            'label' => 'Html View Modules',
                            'route' => 'edm-admin-default',
                            'privilege' => 'cms-author',
                            'resource' => 'index/view-module/html'
                        )
                    )
                ),
            )
        ),
        array(
            'label' => 'System',
            'privilege' => 'cms-super',
            'uri' => '',
            'pages' => array(
                array(
                    'label' => 'Terms',
                    'route' => 'edm-admin/term',
                    'resource' => 'term',
                    'privilege' => 'cms-super',
                    'controller' => 'TermController',
                    'pages' => array(
                        array(
                            'label' => 'Term Create',
                            'route' => 'edm-admin-default',
                            'privilege' => 'cms-author',
                            'resource' => 'create'
                        ),
                    )
                ),
                array(
                    'label' => 'Term Taxonomies',
                    'route' => 'edm-admin/term-taxonomy',
                    'privilege' => 'cms-user',
                    'resource' => 'term-taxonomy',
                    'pages' => array(
                        array(
                            'label' => 'Term Taxonomy Create',
                            'route' => 'updateOrDelete',
                            'privilege' => 'cms-author',
                            'resource' => 'create'
                        ),
                    )
                ),
                array(
                    'label' => 'Users',
                    'route' => 'edm-admin/user',
                    'privilege' => 'cms-user',
                    'resource' => 'user',
                    'pages' => array(
                        array(
                            'label' => 'User Create',
                            'route' => 'edm-admin/user/create',
                            'privilege' => 'cms-manager',
                            'resource' => 'create'
                        ),
                    )
                ),
                
            ),
        )
    )
);
