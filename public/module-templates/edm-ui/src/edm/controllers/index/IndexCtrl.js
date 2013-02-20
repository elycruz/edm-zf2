define([
    'library/navigation/ViewStack', 
    'configs/SystemNavData', 
    'angular'], function (ViewStack, navData) {
    
    function IndexCtrl ($scope) {
        $scope.name = 'Hello world 2x';
        $scope.navigation = navData;
    }
    
    return IndexCtrl;

});

