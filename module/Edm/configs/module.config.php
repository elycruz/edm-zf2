<?php

//-----------------------------------------------------------------------------
// Defines
//-----------------------------------------------------------------------------

// Application path
defined('APP_PATH') ||
        define('APP_PATH', realpath(__DIR__ . '/../../../'));

// Edm salt seed
defined('EDM_SALT') ||
    define('EDM_SALT', 'youruniquesalttextgoeshere');

// Edm pepper seed
defined('EDM_PEPPER') ||
    define('EDM_PEPPER', 'youruniquepeppertextgoeshere');

// Edm token seed
defined('EDM_TOKEN_SEED') ||
    define('EDM_TOKEN_SEED', 'tokenseedtextgoeshere');

// Module Configuration
return array(
    'service_manager' => array(
        'invokables' => array(
            
            // Service invokables
            'Edm\Service\TermTaxonomyService'   => 'Edm\Service\TermTaxonomyService',
            'Edm\Service\UserService'           => 'Edm\Service\UserService',

            // Db invokables
            'Edm\Db\DatabaseDataHelper'         => 'Edm\Db\DatabaseDataHelper',
            'Edm\Db\Table\TermTable'            => 'Edm\Db\Table\TermTable',
            'Edm\Db\Table\TermTaxonomyTable'    => 'Edm\Db\Table\TermTaxonomyTable',
            'Edm\Db\Table\UserTable'            => 'Edm\Db\Table\UserTable',
            'Edm\Db\Table\ContactTable'         => 'Edm\Db\Table\ContactTable',
            
            // Form invokables
            'Edm\Form\TermTaxonomyForm'         => 'Edm\Form\TermTaxonomyForm',
            
            // Config invokables
            // --------------------------------------------------------------
            // Shared global input filter options (mostly validators and filters)
            'Edm\InputFilter\DefaultInputOptions' 
                => 'Edm\InputFilter\DefaultInputOptions',
            
            'Zend\Authentication\AuthService' => 
                'Zend\Authentication\AuthenticationService'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Edm\Controller\Index'        => 'Edm\Controller\IndexController',
            'Edm\Controller\Term'         => 'Edm\Controller\TermController',
            'Edm\Controller\TermTaxonomy' => 'Edm\Controller\TermTaxonomyController',
            'Edm\Controller\User'         => 'Edm\Controller\UserController',
            'Edm\Controller\Post'         => 'Edm\Controller\PostController',
            'Edm\Controller\AjaxUi'       => 'Edm\Controller\AjaxUiController',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'edmFormElement' => 'Edm\Form\Helper\EdmFormElement'
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
                        'controller' => 'index',
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
                            'updateOrDelete' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '[/id/:itemId]',
                                    'constraints' => array(
                                        'itemId' => '[a-zA-Z\d\_\-]+'
                                    ),
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
                                    '[/accessGroup/:accessGroup]' .
                                    '[/role/:role]' .
                                    '[/status/:status]' .
                                    
                                    '[/parent_id/:parent_id]',
                                    '[/filter/:filter][/filterBy/:filterBy]',
                                    'constraints' => array(
                                        'page' => '\d*',
                                        'sort' => '[a-zA-Z0-1]*',
                                        'sortBy' => '[a-zA-Z\d_\-]*',
                                        'filter' => '[a-zA-Z\d_\-]*',
                                        'filterBy' => '[a-zA-Z0-9\d_\-]*',
                                        'taxonomy' => '[a-zA-Z0-9\d_\-\*]*',
                                        'accessGroup' => '[a-zA-Z0-9\d_\-\*]*',
                                        'role' => '[a-zA-Z0-9\d_\-\*]*',
                                        'status' => '[a-zA-Z0-9\d_\-\*]*',
                                        'parent_id' => '[a-zA-Z0-9_\-]*',
                                        'itemsPerPage' => '\d*',
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
            'layout/layout'     => APP_PATH .'/public/module-templates/edm-ko-ui/login.phtml',
            'layout/ajax-ui'  => APP_PATH . '/public/module-templates/edm-ko-ui/index.phtml'
        )
    ),
);
