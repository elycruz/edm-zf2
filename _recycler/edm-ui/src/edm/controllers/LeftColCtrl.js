define([
//    'library/navigation/ViewStack', 
    'configs/SystemNavData', 
    'configs/ContentNavData', 
    'configs/ViewModuleNavData', 
    'library/directives/UlMenu',
    'library/directives/LeftColModules',
    'angular'], function (navData, navData2, navData3) {
    
    function LeftColCtrl ($scope) {
        $scope.navData = [navData, navData2, navData3];
    }
    
    return LeftColCtrl;

});