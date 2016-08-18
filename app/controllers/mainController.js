(function(){


    var mainController = function($scope, $timeout, $interval, mainService, mineFactory, $location){
        $scope.menuOpen = false;
        $scope.headerFixed = true;
        $scope.asideFixed = false;
        //-------------------------------------------------------------------------------------------
        /**
         * Open close menu action
         */
        $scope.openMenu = function(){
            $scope.menuOpen = $scope.menuOpen===true ? false : true;
            if($scope.menuOpen){
                $('.menu_a').off('mouseenter mouseleave', '>li');
                $timeout(function(){
                    mainService.submenuBaction();
                    $('.active').next().slideDown( "fast");
                },500);
            } else {
                $('.menu_b').off('mouseup', '>li>a');
                $timeout(function(){mainService.submenuAaction();},500);
            }

        };

       

        $scope.sessionCoditioner = function(){
            mineFactory.sessionConditioner().success(function(msg){
                if(msg.success){
                    console.log('logged in');
                } else {
                    //not loged in
                    $scope.logoutUser();
                }   
            }).error(function(error){
                console.log(error);
            })
        };

         $scope.sessionCoditioner();

        $interval(function(){ $scope.sessionCoditioner(); }, 60000);

        //-------------------------------------------------------------------------------------------

        $scope.show = function(){
            alert($scope.windowHeight+', '+$scope.windowWidth);
        };

        //-------------------------------------------------------------------------------------------
        /**
         * set ngClass name active
         * expect param string or object
         */
        $scope.menuClass = function(page) {
            var current = $location.path().substring(1);
            current = current.split('/');
            if (current instanceof Array){
                current = current[0];
            } else {
                current = current;
            }

            if(typeof page === 'object'){
               for(var i=0; i<page.length; i++){
                   var pg = page[i].split("/");
                   if(pg[0] === current){
                       return "active";
                   };
               }
            }else {
               return page === current ? "active" : "";
            }
        };

        $scope.logoutUser = function(){
            window.location.href = mainService.domainURL()+'login/logout';
        };


        //-------------------------------------------------------------------------------------------
        /**
         * Initiation
         */
        var init = function(){
            if($scope.menuOpen===false){
                $timeout(function(){mainService.submenuAaction();},500);
            }else {
                $timeout(function(){mainService.submenuBaction();},500);
            }
        };

        init();

    };
    mainController.$inject = ['$scope', '$timeout', '$interval', 'mainService', 'mineFactory', '$location'];
    angular.module('_raiffisenApp')
        .controller('mainController', mainController);





}());