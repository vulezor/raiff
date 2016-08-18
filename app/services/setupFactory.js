(function(){
    var setupFactory = function($http){
        var factory = {};

        factory.getSetupParams = function(){
            return $http.get('setup_api/get_params');
        };
        factory.updateSrps = function(data){
            return $http.put('setup_api/update_srps', data);
        };

        factory.updateBonifikacija = function(data){
            return $http.put('setup_api/update_bonifikacija', data);
        };

        factory.updateKukuruzTabela = function(data){
            return $http.put('setup_api/update_kukuruz_tabela', data);
        };
        factory.updatePsenicaTabela = function(data){
            return $http.put('setup_api/update_psenica_tabela', data);
        };
        factory.updateObracunVlage = function(data){
            return $http.put('setup_api/update_nacin_obracuna_vlage', data);
        };

        return factory;
    };
    setupFactory.$inject = ['$http'];
    angular.module('_raiffisenApp').factory('setupFactory', setupFactory);

}());