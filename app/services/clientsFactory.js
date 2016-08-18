(function(){
    var clientsFactory = function($http){
        var factory = {};

        factory.getClients = function(){
            return $http.get('client_api/get_clients');
        };

        factory.getClient = function(id){
            return $http.get('client_api/get_clients/' + id);
        };

        factory.insertClient = function(client){
            return $http.post('client_api/insert_client', client);
        };

        factory.updateClient = function(client){
            return $http.put('client_api/update_client/' + client.client_id, client);
        };

        factory.deleteClient = function (client) {
            return $http.delete('client_api/delete_client/' + client);
        };

        return factory;
    };
    clientsFactory.$inject = ['$http'];
    angular.module('_raiffisenApp')
        .factory('clientsFactory', clientsFactory);
//-------------------------------------------------------------------------------

}());