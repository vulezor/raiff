(function(){
    var placesFactory = function($http){
        var factory = {};

        factory.getPlaces = function(){
            return $http.get('place_api/get_places');
        };

        factory.getPlace = function(id){
            return $http.get('place_api/get_places/' + id);
        };

        factory.insertPlace = function(place){
            return $http.post('place_api/insert_places', place);
        };

        factory.updatePlace = function(place){
            return $http.put('place_api/update_places/' + place.place_id, place);
        };

        factory.deletePlace = function (id) {
            return $http.delete('api/delete_place/' + id);
        };


        return factory;
    };
    placesFactory.$inject = ['$http'];
    angular.module('_raiffisenApp')
        .factory('placesFactory', placesFactory);
}());