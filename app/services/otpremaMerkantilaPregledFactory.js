(function(){
    var otpremaMerkantilaPregledFactory = function($http, mainService){
        var factory = {};
		
		 factory.get_search_type = function(data){
            return $http.get(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_search_type/', { params: data});
        };
		
		factory.get_search_good_type_admin = function(data){
            return $http.get(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_search_good_type_admin/', { params: data });
        };


		factory.get_search_good_name = function(data){
            return $http.get(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_search_good_name_admin/', { params: data });
        };

        factory.get_search_good_wearehouses_admin = function(data){
            return $http.get(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_search_good_wearehouses_admin/', { params: data });
        };
		
		factory.get_search_good_client = function(data){
            return $http.get(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_search_good_client_admin/', { params: data });
        };
        factory.get_search_prijem_total = function(data){
            return $http.get(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_search_otprema_total_admin/', { params: data });
        };
        factory.storniraj_dokument = function(data){
            return $http.post(mainService.domainURL()+'pregled_otpreme_merkantile_api/storniraj_dokument/', data);
        };

        /*merkantila izlaz*/
        factory.get_wearehouses = function(){
            return $http.get(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_wearehouses/');
        };

        factory.getGoodsType = function(){
            return $http.get(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_goods_type/');
        };
        factory.getGoodsName = function(goods_type_id){
            return $http.get(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_goods_name/'+goods_type_id);
        };
        factory.insertMerkantila = function(merkantila){
            return $http.post(mainService.domainURL()+'pregled_otpreme_merkantile_api/otprema_merkantila', merkantila);
        };
        factory.selectLastOutput = function(){
            return $http.post(mainService.domainURL()+'pregled_otpreme_merkantile_api/select_last_output');
        };
        factory.enableDays = function(){
            return $http.post(mainService.domainURL()+'pregled_otpreme_merkantile_api/enable_days');
        };
        factory.getSearchType = function(data){
            return $http.post(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_search_type', data);
        };
        factory.getSearchMerkantilaName = function(data){
            return $http.post(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_search_good_name_prijem', data);
        };
        factory.getSearchOtpremnica = function(data){
            return $http.post(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_search_otpremnica', data);
        };
        factory.selectOdabranuOtpremu = function(data){
            return $http.post(mainService.domainURL()+'pregled_otpreme_merkantile_api/select_odabranu_otpremu', data);
        };

        return factory;
    };
    otpremaMerkantilaPregledFactory.$inject = ['$http', 'mainService'];
    angular.module('_raiffisenApp').factory('otpremaMerkantilaPregledFactory', otpremaMerkantilaPregledFactory);
}());