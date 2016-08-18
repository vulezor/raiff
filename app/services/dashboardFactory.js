(function(){
    var dashboardFactory = function($http, mainService){
        var factory = {};
        factory.getWearehouseInfo =  function(){
            return $http.get(mainService.domainURL()+'dashboard_api/get_wearehouses');
        };
        factory.stanjeMagacinaInfo = function(data){
            return $http.get(mainService.domainURL()+'stanje_repromaterijala_api/get_results', {params:data});
        };
        factory.getDayOutputs = function(data){
            return $http.get(mainService.domainURL()+'dashboard_api/getDayOutputs', {params:data});
        };
        factory.getOtpremnica = function(data){
            return $http.get(mainService.domainURL()+'pregled_otpreme_repromaterijal_admin_api/getOtprema', {params:data});
        };
        factory.enableDays = function(data){
            return $http.get(mainService.domainURL()+'dashboard_api/enableDays', {params:data});
        };
        return factory;
    };
    dashboardFactory.$inject = ['$http', 'mainService'];
    angular.module('_raiffisenApp').factory('dashboardFactory', dashboardFactory);
}());