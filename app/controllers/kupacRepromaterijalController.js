(function(){
    var kupacRepromaterijalController = function($scope, kupacRepromaterijalFactory, mainService){
        $scope.hideKupac = false;
        $scope.kupci = []; //kupci objekt
        $scope.trazi_kupca = ''; //filter varijabla

        $scope.odabrani_kupac = ''; //odabrani kupac varijabla
        $scope.odabrani_firm_name = '';


        $scope.category_menu_items = {};

        $scope.category_good_items = {};

        $scope.client_results = [];
        //--------------------------------------------------------------------------------------------------------------------

        /**
         * sakriva panel kupaca
         */
        $scope.hide = function(){
            $scope.hideKupac = !$scope.hideKupac;
            $scope.trazi_kupca = '';
            window.setTimeout(function(){
                $('input[name="filter_kupca"]').focus();
            }, 200);
        };

        //--------------------------------------------------------------------------------------------------------------------
        /**
         * poziva sve kupce koji su zavedeni i grupisani u otpremi repromaterijala
         */
        kupacRepromaterijalFactory.getKupce().success(function(msg){
            if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false) {
                //console.log(msg);
                $scope.daily_outputs = msg;
                $scope.kupci = msg;
            } else {
                window.location.href = mainService.domainURL();//logout
            }
        }).error(function(error){
            console.log(error);
        });

        //----------------------------------------------------------------------------------------------------------------------
        /**
         * @param kupac_id
         * set $scope.odabrani_kupac
         * set $scope.category_menu_items
         */
        $scope.odabirKupca = function(kupac){
            $('.for_kupca').css( "visibility", "visible");
            $scope.odabrani_kupac = kupac.client_id;
            $scope.odabrani_firm_name = kupac.firm_name;
            kupacRepromaterijalFactory.odabirKupca({client_id:$scope.odabrani_kupac}).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false) {
                    console.log(msg);
                    $scope.category_menu_items = msg;
                    $scope.client_results = [];
                    $('.for_kupca').css( "visibility", "hidden");
                    $scope.hide();
                } else {
                    window.location.href = mainService.domainURL();//logout
                }
            }).error(function(error){
                console.log(error);
            });
        };

        $scope.showGoods = function(index, good_id){
            var data
            if(typeof index==='undefined' && typeof good_id === 'undefined'){
                data = {
                    'client_id': $scope.odabrani_kupac
                }
            } else if(typeof index !== 'undefined' && typeof good_id === 'undefined'){
                data = {
                    'client_id': $scope.odabrani_kupac,
                    'type_of_goods_id':$scope.category_menu_items[index].type_of_goods_id
                }
            } else {
                data = {
                    'client_id': $scope.odabrani_kupac,
                    'type_of_goods_id':$scope.category_menu_items[index].type_of_goods_id,
                    'goods_id':good_id
                }
            }
            kupacRepromaterijalFactory.getGoods(data).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false) {
                    console.log(msg);
                    $scope.client_results = msg;
                } else {
                    window.location.href = mainService.domainURL();//logout
                }
            }).error(function(error){
                console.log(error);
            });
        };

        var timeout = 0;
        $('.category_menu').on('mouseenter','li', function(){
            $(this).find('ul').css({"display":"block"});
        }).on('mouseleave','li', function(){
            $(this).find('ul').css({"display":"none"});
        });



    };



    kupacRepromaterijalController.$inject = ['$scope', 'kupacRepromaterijalFactory', 'mainService'];
    angular.module('_raiffisenApp').controller('kupacRepromaterijalController', kupacRepromaterijalController);
}());