<?php

// @todo remove all old `array()` invocations.
// Module Configuration
return array(

    'service_manager' => array(
        'invokables' => array(
            
            // Service invokables
            'Edm\Service\TermTaxonomyService'   => 'Edm\Service\TermTaxonomyService',
            'Edm\Service\UserService'           => 'Edm\Service\UserService',
            //'Edm\Service\ViewModuleService'     => 'Edm\Service\ViewModuleService',
            'Edm\Service\PostService'           => 'Edm\Service\PostService',
            //'Edm\Service\PageService'           => 'Edm\Service\PageService',

            // Db invokables
            'Edm\Db\DatabaseDataHelper'                  => 'Edm\Db\DatabaseDataHelper',
            'Edm\Db\TableGateway\ContactTable'           => 'Edm\Db\TableGateway\ContactTable',
            'Edm\Db\TableGateway\ContactUserRelTable'    => 'Edm\Db\TableGateway\ContactUserRelTable',
            'Edm\Db\TableGateway\DateInfoTable'          => 'Edm\Db\TableGateway\DateInfoTable',
            'Edm\Db\TableGateway\PostCategoryRelTable'   => 'Edm\Db\TableGateway\PostCategoryRelTable',
            'Edm\Db\TableGateway\PostTable'              => 'Edm\Db\TableGateway\PostTable',
            'Edm\Db\TableGateway\TermTable'              => 'Edm\Db\TableGateway\TermTable',
            'Edm\Db\TableGateway\TermTaxonomyProxyTable' => 'Edm\Db\TableGateway\TermTaxonomyProxyTable',
            'Edm\Db\TableGateway\TermTaxonomyTable'      => 'Edm\Db\TableGateway\TermTaxonomyTable',
            'Edm\Db\TableGateway\UserTable'              => 'Edm\Db\TableGateway\UserTable',

            // Form invokables
            'Edm\Form\UserForm'           => 'Edm\Form\UserForm',
            'Edm\Form\TermForm'           => 'Edm\Form\TermForm',
            //'Edm\Form\TermTaxonomyForm'   => 'Edm\Form\TermTaxonomyForm',

            // Config invokables
            // --------------------------------------------------------------
            // Shared global input filter options (mostly validators and filters)
            'Edm\InputFilter\DefaultInputOptions' 
                => 'Edm\InputFilter\DefaultInputOptions',
        )
    ),
    'aliases' => [
        // Service aliases
        'edm-post-service'          => 'Edm\Service\PostService',
        'edm-term-taxonomy-service' => 'Edm\Service\TermTaxonomyService',
        'edm-user-service'          => 'Edm\Service\TermTaxonomyService'
    ],
    /*'controllers' => array(
        'invokables' => array(
            'Edm\Controller\Index'        => 'Edm\Controller\IndexController',
            'Edm\Controller\Term'         => 'Edm\Controller\TermController',
            'Edm\Controller\TermTaxonomy' => 'Edm\Controller\TermTaxonomyController',
            'Edm\Controller\ViewModule'   => 'Edm\Controller\ViewModuleController',
            'Edm\Controller\Menu'         => 'Edm\Controller\MenuController',
            'Edm\Controller\User'         => 'Edm\Controller\UserController',
            'Edm\Controller\Post'         => 'Edm\Controller\PostController',
            'Edm\Controller\Page'         => 'Edm\Controller\PageController',
            'Edm\Controller\AjaxUi'       => 'Edm\Controller\AjaxUiController',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'edmFormElement' => 'Edm\Form\Helper\EdmFormElement',
            'edmFormCollection' => 'Edm\Form\Helper\EdmFormCollection'
        )
    ),
    'router' => array(
        'routes' => array(
            'edm-admin-default' => array(
                'type' => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/edm-admin',
                    'defaults' => array(
                        // Set for Edm Access Gateway control
                        'module' => 'edm-admin',
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Edm\Controller',
                        'controller' => 'Edm\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                        ),
                        'child_routes' => array(
                            'flashMessagesToJson' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '[/message-namespace-prefix/:prefix]',
                                    'constraints' => array(
                                        'prefix' => '[a-zA-Z\d\_\-]+'
                                    )
                                )
                            ),
                            'updateOrDelete' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '[/id/:itemId]' + 
                                        '[type=:type]',
                                    'constraints' => array(
                                        'itemId' => '[a-zA-Z\d\_\-]+',
                                        'type' => '[a-zA-Z_]+'
                                    )
                                )
                            ),
                            'updateListOrder' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '[/id/:itemId]' .
                                    '[/listOrder/:listOrder]',
                                    'constraints' => array(
                                        'itemId' => '[a-zA-Z\d\_\-]+',
                                        'listOrder' => '[\d]+'
                                    ),
                                )
                            ),
                            'createSubObject' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '[/id/:itemId]' .
                                    '[/type/:type]',
                                    'constraints' => array(
                                        'itemId' => '[a-zA-Z\d\_\-]+',
                                        'type' => '[a-zA-Z\d\_\-]+',
                                    ),
                                )
                            ),
                            // @todo figure out a more compact way of doing this
                            // @todo maybe generate it dynamically at the top if no other solution
                            'paginator' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' =>
                                    '[/page/:page]' .
                                    '[/itemsPerPage/:itemsPerPage]' .
                                    '[/sort/:sort][/sortBy/:sortBy]' .
                                    '[/taxonomy/:taxonomy]' .
                                    '[/term_taxonomy_id/:term_taxonomy_id]' .
                                    '[/accessGroup/:accessGroup]' .
                                    '[/role/:role]' .
                                    '[/status/:status]' .
                                    
                                    '[/parent_id/:parent_id]',
                                    '[/filter/:filter][/filterBy/:filterBy]',
                                    'constraints' => array(
                                        'page' => '\d*',
                                        'itemsPerPage' => '\d*',
                                        'sort' => '[a-zA-Z0-1]*',
                                        'sortBy' => '[a-zA-Z\d_\-]*',
                                        'filter' => '[a-zA-Z\d_\-]*',
                                        'filterBy' => '[a-zA-Z0-9\d_\-]*',
                                        'term_taxonomy_id' => '[\d\*]*',
                                        'taxonomy' => '[a-zA-Z0-9\d_\-\*]*',
                                        'accessGroup' => '[a-zA-Z0-9\d_\-\*]*',
                                        'role' => '[a-zA-Z0-9\d_\-\*]*',
                                        'status' => '[a-zA-Z0-9\d_\-\*]*',
                                        'parent_id' => '[a-zA-Z0-9_\-]*',
                                        // @todo fix this so that it is either or
                                    ),
                                    'defaults' => array(
                                        'page' => 1,
                                        'itemsPerPage' => 10
                                    )
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Edm' => __DIR__ . '/../view-scripts',
        ),
        'template_map' => array(
            'layout/edm-admin-login'    => APP_PATH .'/public/module-templates/edm-polymer-ui/login.phtml',
            'layout/edm-admin-ajax-ui'  => APP_PATH . '/public/module-templates/edm-polymer-ui/index.phtml'
        )
    ),*/
);
