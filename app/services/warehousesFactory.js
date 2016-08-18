(function(){
    var wearehouseFactory = function($http){
        var factory = {};

        factory.getWearehouses = function(){
            return $http.get('wearehouse_api/get_wearehouse');
        };

        factory.getWearehouse = function(id){
            return $http.get('wearehouse_api/get_wearehouse/' + id);
        };

        factory.insertWearehouse = function(wearehouse){
            return $http.post('wearehouse_api/insert_wearehouse', wearehouse);
        };

        factory.updateWearehouse = function(wearehouse){
            return $http.put('wearehouse_api/update_wearehouse/' + wearehouse.wearehouse_id, wearehouse);
        };

        factory.deleteWearehouse = function (id) {
            return $http.delete('api/delete_wearehouse/' + id);
        };


        return factory;
    };
    wearehouseFactory.$inject = ['$http'];
    angular.module('_raiffisenApp')
        .factory('wearehouseFactory', wearehouseFactory);
//-------------------------------------------------------------------------------

}());