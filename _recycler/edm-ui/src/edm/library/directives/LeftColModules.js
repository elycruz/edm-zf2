define(['jquery', 'angular'], function () {
    
    // Left col module
//    edm.core.directive('leftColModule', function () {
//        return {
//            priority    : 999,
//            restrict    : 'A',
//            transclude  : true,
//            replace     : true,
//            template    : $('#left-col-menu-module-tmpl').html()
//        };
//    }); 
    
    // Left col menu module
    edm.core.directive('leftColMenuModule', function () {
        return {
            priority    : 999,
            restrict    : 'A',
            template    : $('#left-col-menu-module-tmpl').html(),
            scope       : {
                module: '='
            },
            link: function (scope) {
                scope.leak = 'xxx';
            }
        };
    });
    
});