(function(){
    var mineFactory = function($http, mainService){
        var factory = {};
        factory.sessionConditioner =  function(){
            return $http.get(mainService.domainURL()+'dashboard_api/session_conditioner');
        };
        return factory;
    };
    mineFactory.$inject = ['$http', 'mainService'];
    angular.module('_raiffisenApp').factory('mineFactory', mineFactory);
    }());