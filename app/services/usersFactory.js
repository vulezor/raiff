(function(){
    var usersFactory = function($http){
        var factory = {};

        factory.getUsers = function(){
            return $http.get('user_api/get_users');
        };

        factory.getUser = function(id){
            return $http.get('user_api/get_users/' + id);
        };

        factory.insertUser = function(user){
            return $http.post('user_api/insert_user', user);
        };

        factory.updateUser = function(user){
            return $http.put('user_api/update_user/' + user.user_id, user);
        };

        factory.updateUserActivity = function(user){
            return $http.put('user_api/update_activity/' + user.user_id, user);
        };

        factory.deleteUser = function (id) {
            return $http.delete('api/delete_user/' + id);
        };

        return factory;
    };
    usersFactory.$inject = ['$http'];
    angular.module('_raiffisenApp')
        .factory('usersFactory', usersFactory);
//-------------------------------------------------------------------------------

}());