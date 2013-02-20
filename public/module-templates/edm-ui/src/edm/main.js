/**
 * Entry point file.
 * Contains exports variable and defines some top level configuration for require
 * @created 09052012
 * @lastupdated 12022012
 * @author ElyDeLaCruz
 * @license mit & gpl-v2 +
 */

/**
 * Our only global variable
 * @var <object>
 */
var edm = {
    services: null,
    directives: null
};

// //fedls : front-end-downloads or your own url to resources
require.config({
    paths: {
        'es5'         :         'http://fedls/es5-shim',
        'es6'         :         'http://fedls/es6-shim',
        'phpjs'       :         'http://github/phpjs/php-0.0.3',
        
        //        'amplify'     :         'http://fedls/amplify-1.1.0/amplify.min',
        //        'history'     :         'http://github/history.js/scripts/compressed/history',
        'less-js'     :         'http://fedls/less-1.3.0.min',
        
        'jquery'      :         'http://fedls/jquery-1.9.1.min',
        'angular'     :         'http://fedls/angular',

        // Require plugins
        'depend'      :         'http://github/requirejs-plugins/src/depend',
        'text'        :         'http://fedls/text'
    }
});

// Main loop
define([
    'es5', 
    'phpjs', 
    'jquery', 
    'depend!angular[jquery]',
    'less-js',
    'text'
    ], function () {
        
        // Init Edm Module
        edm.directives = angular.module('edm.directives', []);
        edm.core = angular.module('edm', ['edm.directives']);
        
        // Init app
        require(['Init']);
        
    });
    