define([
    'library/navigation/View', 
    'library/navigation/ViewStack',
    'jquery', 'angular'], function(View, ViewStack) {
    
        edm.core.directive('module', function () {
            return {
                priority    : 1000,
                restrict    : 'A',
                template    : $('#module-tmpl').html(),
                transclude  : true,
                replace     : true,
                controller  : function ($scope) {
                    
                }
            };
        });
        
//        
//        edm.core.directive('ulMenu', function ($compile) {
//            return {
//                priority    : 1000,
//                restrict    : 'E',
//                transclude  : true,
//                replace     : true,
//                template    : $('#ul-menu-tmpl').html(),
//                scope       : {
//                    items: '=items'
//                },
//                controller  : function ($scope) {},
//                link: function (scope, elm) {
//                    if (scope.items.length > 0) {
//                    }
//                }
//            };
//        });
        
    });