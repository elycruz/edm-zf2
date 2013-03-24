define([
    'text!view-templates/index/tmpls.html',
    'controllers/MainNavCtrl',
    'controllers/LeftColCtrl',
    'controllers/DemoCtrl',
    'jquery',
    'angular'
],
        function(tmpls, MainNavCtrl, LeftColCtrl, DemoCtrl) {
            $(function() {
                // Append top level templates
                var body = $('body').eq(0);
                body.append(tmpls);
                
                // List controllers and their dependencies
                edm.core.controller('MainNavCtrl', ['$scope', MainNavCtrl]);
                edm.core.controller('LeftColCtrl', ['$scope', LeftColCtrl]);
                edm.core.controller('DemoCtrl', ['$scope', DemoCtrl]);
                
                // Bootstrap Angular
                angular.bootstrap(document, ['edm']);

//
//                // Make drop down menus
//                $('li ul', mainNav).each(function() {
//                    $(this).css('display', 'none');
//                    var p = $(this).parent();
//                    p.hover(function() {
//                        var ul = p.find('ul');
//                        //ul.fadeIn( 'fast' ).show();
//                        ul.slideDown('fast');
//                    },
//                            function() {
//                                var ul = p.find('ul');
//                                //ul.fadeOut( 'fast' );
//                                ul.slideUp('fast');
//                            });
//                }); // drop down menus top

            });

        }); // define
