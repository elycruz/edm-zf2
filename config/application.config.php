<?php

ini_set('date.timezone', 'America/New_York');

return array(
    'modules' => array(
        'Application',
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
