(function(){
    var dispozicijeFactory = function($http, mainService){
        var factory = {};

        factory.getAllGoods = function(data){
            return $http.get(mainService.domainURL()+'dispozicije_api/get_all_goods', {params:data});
        };

        factory.saveDisposition = function(data){
            return $http.post(mainService.domainURL()+'dispozicije_api/save_disposition', data);
        };

        factory.storniraj_dokument = function(data){
            return $http.post(mainService.domainURL()+'dispozicije_api/storniraj_dokument/', data);
        };
        factory.getDisposition = function(data){
            return $http.get(mainService.domainURL()+'dispozicije_api/view_dispositionAdmin/', {params:data});
        };
        factory.changeKolicinu = function (data) {
            return $http.post(mainService.domainURL()+'dispozicije_api/change_kolicinuAdmin/', data);
        };

        return factory;
    };
    dispozicijeFactory.$inject = ['$http','mainService'];
    angular.module('_raiffisenApp').factory('dispozicijeFactory', dispozicijeFactory);
}());