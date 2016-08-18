(function(){
    var stanjeRepromaterijalFactory = function($http, mainService){
        var factory = {};

        factory.get_wearehouses = function(){
            return $http.get(mainService.domainURL()+'stanje_repromaterijala_api/get_wearehouses');
        };
        factory.get_results = function(data){
            return $http.get(mainService.domainURL()+'stanje_repromaterijala_api/get_results', {params: data});
        };

        return factory;
    };
    stanjeRepromaterijalFactory.$inject = ['$http', 'mainService'];
    angular.module('_raiffisenApp').factory('stanjeRepromaterijalFactory', stanjeRepromaterijalFactory);
//-------------------------------------------------------------------------------

}());