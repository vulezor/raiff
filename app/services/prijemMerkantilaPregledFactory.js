(function(){
    var prijemMerkantilaPregledFactory = function($http, mainService){
        var factory = {};
		
		 factory.get_search_type = function(data){
            return $http.get(mainService.domainURL()+'pregled_prijema_merkantile_api/get_search_type/', { params: data});
        };
		
		factory.get_search_good_type_admin = function(data){
            return $http.get(mainService.domainURL()+'/pregled_prijema_merkantile_api/get_search_good_type_admin/', { params: data });
        };


		factory.get_search_good_name = function(data){
            return $http.get(mainService.domainURL()+'pregled_prijema_merkantile_api/get_search_good_name_admin/', { params: data });
        };

        factory.get_search_good_wearehouses_admin = function(data){
            return $http.get(mainService.domainURL()+'pregled_prijema_merkantile_api/get_search_good_wearehouses_admin/', { params: data });
        };
		
		factory.get_search_good_client = function(data){
            return $http.get(mainService.domainURL()+'pregled_prijema_merkantile_api/get_search_good_client_admin/', { params: data });
        };
        factory.get_search_prijem_total = function(data){
            return $http.get(mainService.domainURL()+'pregled_prijema_merkantile_api/get_search_prijem_total_admin/', { params: data });
        };
        factory.storniraj_dokument = function(data){
            return $http.post(mainService.domainURL()+'pregled_prijema_merkantile_api/storniraj_dokument/', data);
        };


        /*merkantila unos*/
        factory.get_wearehouses = function(){
            return $http.get(mainService.domainURL()+'pregled_prijema_merkantile_api/get_wearehouses/');
        };

        factory.getGoodsType = function(){
            return $http.get(mainService.domainURL()+'pregled_prijema_merkantile_api/get_goods_type/');
        };
        factory.getGoodsName = function(goods_type_id){
            return $http.get(mainService.domainURL()+'prijem_merkantila_api/get_goods_name/'+goods_type_id);
        };
        factory.insertMerkantila = function(merkantila){
            return $http.post(mainService.domainURL()+'pregled_prijema_merkantile_api/prijem_merkantila', merkantila);
        };
        factory.selectLastInput = function(){
            return $http.post(mainService.domainURL()+'pregled_prijema_merkantile_api/select_last_input');
        };
        factory.enableDays = function(){
            return $http.post(mainService.domainURL()+'pregled_prijema_merkantile_api/enable_days');
        };
        factory.getSearchType = function(data){
            return $http.post(mainService.domainURL()+'pregled_prijema_merkantile_api/get_search_type', data);
        };
        factory.getSearchMerkantilaName = function(data){
            return $http.post(mainService.domainURL()+'pregled_prijema_merkantile_api/get_search_good_name_prijem', data);
        };
        factory.getSearchPrijemnica = function(data){
            return $http.post(mainService.domainURL()+'pregled_prijema_merkantile_api/get_search_prijemnica', data);
        };
        factory.selectOdabraniPrijem = function(data){
            return $http.post(mainService.domainURL()+'pregled_prijema_merkantile_api/select_odabrani_prijem', data);
        };



        return factory;
    };
    prijemMerkantilaPregledFactory.$inject = ['$http', 'mainService'];
    angular.module('_raiffisenApp').factory('prijemMerkantilaPregledFactory', prijemMerkantilaPregledFactory);
}());