define(['jquery', 'angular'], function() {
    edm.core.directive('demoGreet', function($parse) {
        return {
            restrict: 'A',
            link: function(iScope, iElm, iAttrs) {
//                trace('linkingFn(', iScope, iElm, iAttrs, ')');

                iScope.$watch(iAttrs.demoGreet, function (name) {
                    iElm.text('Hello ' + name + '!');
                });
                
                iElm.bind('click', function () {
                   trace('click', Error().stack);
                   iScope.$apply(function () {
                       $parse(iAttrs.demoGreet).assign(iScope, 'abc');
                   });
                });
                
            } // linkFn
            
        }; // return
        
    }); // demo greet
    
}); // define