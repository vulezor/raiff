var underscore = angular.module('underscore', []);
underscore.factory('_', ['$window', function($window) {
    return $window._; // assumes underscore has already been loaded on the page
}]);
var _raiffisenApp = angular.module('_raiffisenApp', ['ngRoute', 'underscore', 'ngSanitize', 'ngAnimate', 'ngResource', 'ngMessages', 'datatables', 'datatables.bootstrap', 'datatables.columnfilter', 'datatables.tabletools', 'datatables.buttons', 'datatables.colvis', 'ngCookies']);
_raiffisenApp.factory('interceptor', function($q){
    return{
        request:function(request){
            console.log('request is done');
            return request;
        },
        response:function(response){
            console.log('response is done');
            return response;
        },
        responseError: function(rejection){
            console.log('Failed with', rejection.status, 'status');
            return $q.reject(rejection);
        }
    }
});
(function(){
    angular.module('_raiffisenApp')
        .config(['$routeProvider', '$httpProvider', function($routeProvider, $httpProvider){
            $httpProvider.interceptors.push('interceptor')
            $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
            $routeProvider
                .when('/open_warehouses', {
                    templateUrl:'app/view/open_warehouses.html',
                    controller:'weareHouseController',
                    controllerAs: 'vm'
                })
                .when('/korisnici', {
                    templateUrl: 'app/view/korisnici.html',
                    controller: 'userController'
                })
                .when('/dashboard', {
                    templateUrl: 'app/view/dashboard.html',
                    controller: 'dashboardController'
                })
                .when('/podesavanja', {
                    templateUrl: 'app/view/setup/setup.html',
                    controller: 'setupController'
                })
                .when('/korisnik/:user_id', {
                    templateUrl: 'app/view/korisnik.html',
                    controller: 'editUserController'
                })
                .when('/klijenti', {
                    templateUrl: 'app/view/dobavljaci_kupci.html',
                    controller: 'clientController'
                })
                .when('/klijenti/kupac_dobavljac/:client_id', {
                    templateUrl: 'app/view/dobavljac_kupac.html',
                    controller: 'editClientController'
                })
                .when('/roba', {
                    templateUrl: 'app/view/roba.html',
                    controller: 'goodsController'
                })
                .when('/roba/izmena_podataka/:goods_id', {
                    templateUrl: 'app/view/roba_edit.html',
                    controller: 'editGoodsController'
                })
                .when('/pregled_prijema/pregled_prijema_merkantila', {
                    templateUrl:'app/view/pregled_prijema/pregled_prijema_merkantila.html',
                    controller: 'pregledPrijemaMerkantila'

                })
                .when('/ulaz_robe/merkantila', {
                    templateUrl:'app/view/ulazrobe_merkantila.html'

                })
                .when('/pregled_otpreme/pregled_otpreme_merkantila', {
                    templateUrl:'app/view/pregled_otpreme/pregled_otpreme_merkantila.html',
                    controller: 'pregledOtpremaMerkantila'

                })
                .when('/pregled_prijema/pregled_prijema_repromaterijal', {
                    templateUrl:'app/view/pregled_prijema/pregled_prijema_repromaterijal.html',
                    controller: 'pregledPrijemRepromaterijalController'

                })
                .when('/pregled_otpreme/pregled_otpreme_repromaterijal', {
                    templateUrl:'app/view/pregled_otpreme/pregled_otpreme_repromaterijal.html',
                    controller: 'pregledOtpremeRepromaterijalController'

                })
                .when('/rezervacije', {
                    templateUrl:'app/view/rezervacije.html',
                    controller: 'rezervacijeController'
                })
                .when('/stanja_magacina/merkantila', {
                    templateUrl:'app/view/stanja_magacina/merkantila.html',
                    controller: 'stanjeMerkantilaController'
                })
                .when('/stanja_magacina/repromaterijal', {
                    templateUrl:'app/view/stanja_magacina/repromaterijal.html',
                    controller: 'stanjeRepromaterijalController'
                })
                .when('/dispozicije', {
                    templateUrl:'app/view/dispozicije.html',
                    controller: 'dispozicijeController'
                })
                .when('/pregled_kupac_dobavljac/pregled_kupac_repromaterijal', {
                    templateUrl:'app/view/pregled_kupac_dobavljac/pregled_kupac_repromaterijal.html',
                    controller: 'kupacRepromaterijalController'
                })
                .otherwise({
                    redirectTo:'/',
                    templateUrl:'app/view/dashboard.html',
                    controller:'dashboardController'
                });
        }]);
}());

