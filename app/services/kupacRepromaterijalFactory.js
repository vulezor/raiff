(function(){
    var kupacRepromaterijalFactory = function($http, mainService){
        var factory = {};
        factory.getKupce =  function(){
            return $http.get(mainService.domainURL()+'kupac_repromaterijal_api/get_kupce');
        };
        factory.odabirKupca =  function(data){
            return $http.get(mainService.domainURL()+'kupac_repromaterijal_api/get_category_items', {params:data});
        };
        factory.getGoods =  function(data){
            return $http.get(mainService.domainURL()+'kupac_repromaterijal_api/get_goods', {params:data});
        };
        return factory;
    };
    kupacRepromaterijalFactory.$inject = ['$http', 'mainService'];
    angular.module('_raiffisenApp').factory('kupacRepromaterijalFactory', kupacRepromaterijalFactory);
}());