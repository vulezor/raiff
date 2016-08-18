(function(){
    var editUserController = function($scope, $filter, $routeParams, placesFactory, wearehouseFactory, usersFactory, errorService){
        $scope.userdata = {};
        $scope.Places = {};
        $scope.Warehouses = {};
        $scope.placeVisible= true;
        //New places
        $scope.loadNewPlace =  false; //hide load icon ng-hide
        $scope.new_places = {}
        //get all places from database
        placesFactory.getPlaces().success(function(msg){
            $scope.Places = msg;
            console.log($scope.Places);
        }).error(function(error){
            console.log(error);
        });

        //all user roles
        $scope.roles = [
            {role:'Administrator',label:'Administrator'},
            {role:'Magacioner',label:'Magacioner'},
            {role:'Redovan korisnik',label:'Redovan korisnik'},
            {role:'Logistika',label:'Logistika'}
        ];

        //get all wearehouses from database
        wearehouseFactory.getWearehouses().success(function(msg){
            $scope.Warehouses = msg;

            console.log($scope.Warehouses);
        }).error(function(error){
            console.log(error);
        });

        usersFactory.getUser($routeParams.user_id)
            .success(function(msg){
                console.log(msg[0].wearehouse_id)
                $scope.userdata = msg[0];
                $scope.userdata.selectedPlaceId = msg[0].place_id;
                $scope.userdata.selectedWarehouseId = msg[0].wearehouse_id;
                $scope.showWarehouseSelect();
            })
            .error(function(error){
                console.log(error);
            });

        //resolve visibility of new place insert container
        $scope.doVisible = function(){
            $scope.placeVisible = $scope.placeVisible===true ? false : true; //show, hide of new place panel on click
        };

        $scope.showWarehouseSelect = function(){
            console.log($scope.userdata.role);
            $scope.wearehouseVisibility = $scope.userdata.role=="Magacioner" ? false : true;
            //$scope.wearehouseVisibility = $scope.placeVisible===true ? false : true; //show, hide of new place panel on click
            //hide load icon
        };

        $scope.addNewPlace = function(){
            if(!$scope.new_places.hasOwnProperty('post_number') || $scope.new_places.post_number==="" || typeof $scope.new_places.post_number === 'undefined'){
                alert("Polje za unos poštanskog broja je obavezno");
                errorService.error_msg($('input[name="post_number"]'), "Polje za unos poštanskog broja je obavezno"); return false;
            }
            if(!$scope.new_places.hasOwnProperty('place_name') || $scope.new_places.place_name==="" || typeof $scope.new_places.place_name === 'undefined'){
                alert("Polje za unos naziva mesta/naselja je obavezno");
                errorService.error_msg($('input[name="place_name"]'), "Polje za unos naziva mesta/naselja je obavezno"); return false;
            }
            $scope.loadNewPlace =  true;

            placesFactory.insertPlace($scope.new_places).success(function(msg){
                console.log(msg);
                $scope.loadNewPlace = false; //hide load icon ng-hide
                if(msg.success === 0) {
                    $scope.placeVisible = true; //hide new place panel

                    $scope.new_places.place_id = parseInt(msg.result); //add returning id in new_place object like place_id;
                    $scope.Places.push($scope.new_places);      //pushing new_place object into all places

                    //selecting new place in select field
                    $scope.userdata.selectedPlaceId = parseInt(msg.result);

                    // rest new insert place inputs
                    $scope.new_places = {}
                } else {
                    alert('Mesto koje ste uneli u polje već postoji u bazi podataka');
                }
            }).error(function(error){
                console.log(error);
            });
        };

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        $scope.addNewUser = function(){

            if(!$scope.userdata.hasOwnProperty('name') || $scope.userdata.name==="" || typeof $scope.userdata.name === 'undefined'){
                errorService.error_msg($('input[name="name"]'), "Polje za unos imena je obavezno"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('surname') || $scope.userdata.surname==="" || typeof $scope.userdata.surname === 'undefined'){
                errorService.error_msg($('input[name="surname"]'), "Polje za unos prezimena je obavezno"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('address') || $scope.userdata.address==="" || typeof $scope.userdata.address === 'undefined'){
                errorService.error_msg($('input[name="address"]'), "Polje za unos adrese je obavezno"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('selectedPlaceId') ||  $scope.userdata.selectedPlaceId===0 || typeof $scope.userdata.selectedPlaceId === 'undefined'){
                errorService.error_msg($('select[name="selectedPlaceId"]'), "Morate izabrati mesto prebivališta"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('brlk') || $scope.userdata.brlk==="" || typeof $scope.userdata.brlk === 'undefined'){
                errorService.error_msg($('input[name="brlk"]'), "Polje za unos broja lične karte je obavezno"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('sup') || $scope.userdata.sup==="" || typeof $scope.userdata.sup === 'undefined'){
                errorService.error_msg($('input[name="sup"]'), "Polje za unos supa je obavezno"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('jmbg') || $scope.userdata.jmbg==="" || typeof $scope.userdata.jmbg === 'undefined'){
                errorService.error_msg($('input[name="jmbg"]'), "Polje za unos jedistvenog matičnog broja građana je obavezno"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('email') || $scope.userdata.email==="" || typeof $scope.userdata.email === 'undefined'){
                errorService.error_msg($('input[name="email"]'), "Polje za unos elektronske pošte je obavezno"); return false;
            }
            if($filter('validEmailFilter')($scope.userdata.email)===false){
                errorService.error_msg($('input[name="email"]'), "Format elektronske pošte koju ste uneli je neisparavan"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('role') || $scope.userdata.role === "" || typeof $scope.userdata.role === 'undefined') {
                errorService.error_msg($('select[name="role"]'), "Morate odabrati ulogu korisniku"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('username') || $scope.userdata.username==="" || typeof $scope.userdata.username === 'undefined'){
                errorService.error_msg($('input[name="username"]'), "Polje za unos korisničkog imena je obavezno"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('password') || $scope.userdata.password==="" || typeof $scope.userdata.password === 'undefined'){
                errorService.error_msg($('input[name="password"]'), "Polje za unos lozinke je obavezno"); return false;
            }
            if($scope.userdata.role==="Magacioner"){
                if(!$scope.userdata.hasOwnProperty('selectedWarehouseId') || $scope.userdata.selectedWarehouseId===null || typeof $scope.userdata.password === 'undefined'){
                    errorService.error_msg($('select[name="selectedWarehouseId"]'), "Dodeljivanje magacina magacioneru je obavezno");return false;
                }
            }
            $('.ajax_load_visibility').css('visibility','visible');
            usersFactory.updateUser($scope.userdata).success(function(msg){
                $('.ajax_load_visibility').css('visibility','hidden');
                if(msg.success===0){
                    alert('Uspešno ste izmenili podatke');
                }else {
                    if(msg.field==='username'){
                        errorService.error_msg($('input[name="username"]'), msg.error_msg);
                    }
                    if(msg.field==='jmbg'){
                        errorService.error_msg($('input[name="jmbg"]'), msg.error_msg);
                    }
                }

                /**/
            }).error(function(error){
                console.log(error);
            });

        };
    };
    editUserController.$inject = ['$scope', '$filter', '$routeParams', 'placesFactory', 'wearehouseFactory', 'usersFactory', 'errorService'];
    angular.module('_raiffisenApp').controller('editUserController', editUserController)
}());