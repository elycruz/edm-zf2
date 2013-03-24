define([
    'configs/MainNavData',
    'library/directives/UlMenu',
    'library/directives/DemoGreet',
    'angular'], function (navData) {
    
    function MainCtrl ($scope) {
        $scope.navData = navData;
    }
    
    return MainCtrl;

});