/**
 * Created by Ely on 5/24/2014.
 */
/**
 * Created by ElyDeLaCruz on 1/11/14.
 */
require.config({

    deps: ['main'],

    shim: {
//        'jquery.ui.core': {
//            deps: ['jquery']
//        },
//        'jquery.ui.widget': {
//            deps: ['jquery', 'jquery.ui.core']
//        },
//        'jquery.mousewheel': {
//            deps: ['jquery.ui.widget']
//        },
//        'jquery.debouncedresize': {
//            deps: ['jquery.ui.widget']
//        },
        main: {
            deps: [
                'modernizr', 'jquery', 'ko', 'sjl'
            ]
        }
    },

    paths: {

        // Pubsub
        'amplify.core': '../../bower_components/amplify/lib/amplify.core',

        // Store
        'amplify.store': '../../bower_components/amplify/lib/amplify.store',

        // Type checking helpers
        'sjl': '../../bower_components/sjljs/sjl',

        'jquery': '../../bower_components/jquery/dist/jquery',

        'ko': '../../bower_components/knockoutjs/dist/knockout.debug',

        'modernizr': '../../bower_components/modernizr/modernizr',

        'text': '../../bower_components/requirejs-text/text',

        // Internal
        'logger': 'lib/utils/logger',

        // Templates path
        'tmpl': 'view-templates'
    }
});