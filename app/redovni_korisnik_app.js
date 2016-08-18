var underscore = angular.module('underscore', []);
underscore.factory('_', ['$window', function($window) {
    return $window._; // assumes underscore has already been loaded on the page
}]);
var _raiffisenApp = angular.module('_raiffisenApp', ['ngRoute', 'underscore', 'ngSanitize', 'ngAnimate', 'ngResource', 'ngMessages', 'datatables', 'datatables.bootstrap', 'datatables.columnfilter', 'datatables.tabletools', 'datatables.buttons', 'datatables.colvis', 'ngCookies']);

(function(){
    angular.module('_raiffisenApp')
        .config(['$routeProvider', '$httpProvider', function($routeProvider, $httpProvider){
            $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
            $routeProvider
                .when('/dashboard', {
                    templateUrl: 'app/view/dashboard.html',
                    controller: 'dashboardController'
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
                .otherwise({
                    redirectTo:'/',
                    templateUrl:'app/view/dashboard.html',
                    controller:'dashboardController'
                });
        }]);
}());

