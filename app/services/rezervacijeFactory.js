(function(){
    var rezervacijaFactory = function($http, mainService){
        var factory = {};

        factory.setReservation = function(data){
            return $http.post(mainService.domainURL()+'reservation_api/set_reservation', data);
        };

        factory.storniraj_dokument = function(data){
            return $http.post(mainService.domainURL()+'reservation_api/storniraj_dokument', data);
        };

        return factory;
    };
    rezervacijaFactory.$inject = ['$http', 'mainService'];
    angular.module('_raiffisenApp').factory('rezervacijaFactory', rezervacijaFactory);
}());