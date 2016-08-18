(function(){
    var prijemRepromaterijalPregledFactory = function($http, mainService){
        var factory = {};

        factory.get_search_type = function(data){
            return $http.get(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/get_search_type/', { params: data});
        };

        factory.get_search_good_type_admin = function(data){
            return $http.get(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/get_search_good_type_admin/', { params: data });
        };

        factory.get_search_good_name_admin = function(data){
            return $http.get(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/get_search_good_name_admin/', { params: data });
        };

        factory.get_search_good_wearehouses_admin = function(data){
            return $http.get(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/get_search_good_wearehouses_admin/', { params: data });
        };

        factory.get_search_good_client_admin = function(data){
            return $http.get(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/get_search_good_client_admin/', { params: data });
        };
        factory.get_search_prijem_total_admin = function(data){
            return $http.get(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/get_search_prijem_total/', { params: data });
        };
        factory.storniraj_dokument = function(data){
            return $http.post(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/storniraj_dokument/', data);
        };
        factory.getPrijem = function(input_id){
            console.log(input_id);
            return $http.get(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/getPrijem/', { params: input_id });
        };
        /*repromaterijal unos*/
        factory.get_wearehouses = function(){
            return $http.get(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/get_wearehouses/');
        };
        factory.get_all_goods = function(data){
            return $http.post(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/get_all_goods', data);
        };
        factory.insertRepromaterijal = function(merkantila){
            return $http.post(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/prijem_repromaterijal', merkantila);
        };
        factory.selectLastInput = function(){
            return $http.post(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/select_last_input');
        };

        return factory;
    };
    prijemRepromaterijalPregledFactory.$inject = ['$http', 'mainService'];
    angular.module('_raiffisenApp').factory('prijemRepromaterijalPregledFactory', prijemRepromaterijalPregledFactory);
}());