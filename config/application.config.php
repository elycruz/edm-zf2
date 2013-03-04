<?php

defined('SALT') || define('SALT', 'saltcontentsgohere');

defined('PEPPER') || define('PEPPER', 'peppercontentsgohere');

return array(
    'modules' => array(
        'Application',
        'EdmSession',
        'Edm'
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
        ),
    ),
);
