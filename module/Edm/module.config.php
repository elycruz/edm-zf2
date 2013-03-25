<?php

// Defines
defined('APPVAR_NAME_ALIAS_REGEX') ||
        define('APPVAR_NAME_ALIAS_REGEX', '/[\w\d]+/i');
defined('APP_PATH') ||
        define('APP_PATH', realpath(__DIR__ . '/../../'));

return array(
    'service_manager' => array(
        'invokables' => array(
            'Edm\Service\TermTaxonomyService' => 'Edm\Service\TermTaxonomyService',
            'Edm\Db\DatabaseDataHelper' => 'Edm\Db\DatabaseDataHelper',
            'Edm\Db\Table\TermTable' => 'Edm\Db\Table\TermTable',
            'Edm\Db\Table\TermTaxonomyTable' => 'Edm\Db\Table\TermTaxonomyTable',
            'Edm\Model\Term' => 'Edm\Model\Term',
            'Edm\Model\TermTaxonomy' => 'Edm\Model\TermTaxonomy'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Edm\Controller\Index' => 'Edm\Controller\IndexController',
            'Edm\Controller\Term' => 'Edm\Controller\TermController',
            'Edm\Controller\TermTaxonomy' => 'Edm\Controller\TermTaxonomyController',
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
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Edm\Controller',
                        'controller' => 'Index',
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
//                        'may_terminate' => true,
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
//                                'may_terminate' => true,
                                'options' => array(
                                    'route' => '[/id/:itemId]',
                                    'constraints' => array(
                                        'id' => '[a-zA-Z0-9_\-]'
                                    ),
                                )
                            ),
                            'paginator' => array(
                                'type' => 'Segment',
//                                'may_terminate' => true,
                                'options' => array(
                                    'route' => 
                                        '[/page/:page]' .
                                        '[/itemsPerPage/:itemsPerPage]' .
                                        '[/sort/:sort][/sortBy/:sortBy]' .
                                        '[/filter/:filter][/filterBy/:filterBy]',
                                    'constraints' => array(
                                        'page'          => '\d*',
                                        'sort'          => '[a-zA-Z0-1]*',
                                        'sortBy'        => '[a-zA-Z\d_\-]*',
                                        'filter'        => '[a-zA-Z\d_\-]*',
                                        'filterBy'      => '[a-zA-Z0-9\d_\-]*',
                                        'itemsPerPage'  => '\d*',
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
            'Edm' => __DIR__ . '/src/Edm/view-scripts',
        ),
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../../public/module-templates/edm-ko-ui/index.phtml',
            'partials/message' => __DIR__ . '/src/Edm/view-scripts/edm/partials/message.phtml'
        )
    ),
);
