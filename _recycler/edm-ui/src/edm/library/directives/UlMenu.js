define([
    'library/navigation/View', 
    'library/navigation/ViewStack',
    'jquery', 'angular'], function(View, ViewStack) {
        var ulMenuTmpl;
        edm.core.directive('ulMenu', function () {
            ulMenuTmpl = $('#ul-menu-tmpl').html();
            return {
                priority    : 1,
                restrict    : 'E',
                transclude  : true,
                replace     : true,
                template    : ulMenuTmpl,
                scope       : {
                    items: '='
                }
            };
        });
    
        edm.core.directive('ulMenuLi', function ($compile, $parse) {
            ulMenuTmpl = ulMenuTmpl || $('#ul-menu-tmpl').html();
            return {
                priority    : 1,
                restrict    : 'E',
                replace     : true,
                transclude  : true,
                template    : $('#ul-menu-li-tmpl').html(),
                scope       : {
                    item:  '@',
                    items: '='
                },
                link: function (scope, elm, attrs) {
                    if (isset(scope.item.items) && scope.item.items > 0) {
                        $parse(attrs.items).assign(scope, scope.item.items);
                        elm.append($compile(ulMenuTmpl)(scope));
                    }
                }
            };
        });
        
    });