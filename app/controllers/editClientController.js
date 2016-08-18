(function(){
    var editClientController = function($scope, $filter, $routeParams, infoboxService, placesFactory, wearehouseFactory, clientsFactory, errorService){
        $scope.clientdata = {}
        $scope.new_places = {}
        $scope.placeVisible = true;
        $scope.loadNewPlace = false;

        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


        $scope.client_types = [
            {type:'fizicko',label:'Fizičko'},
            {type:'pravno',label:'Pravno'}
        ];

        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.sisem_pdv = [
            {text:'y',label:'Da'},
            {text:'n',label:'Ne'}
        ];

        clientsFactory.getClient($routeParams.client_id)
            .success(function(msg){
                console.log(msg[0])
                $scope.clientdata = msg[0];
                $scope.clientdata.bank_jib = $scope.clientdata.bank_account.split('-')[0];
                $scope.clientdata.bank_account = $scope.clientdata.bank_account.split('-')[1];
                $scope.clientdata.selectedPlaceId = msg[0].place_id;
                /*$scope.clientdata.selectedWarehouseId = msg[0].wearehouse_id;*/
            })
            .error(function(error){
                console.log(error);
            });


        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        //get all places from database
        placesFactory.getPlaces().success(function(msg){
            $scope.Places = msg;
           /* console.log($scope.Places);*/
        }).error(function(error){
            console.log(error);
        });
        //start infobox mesages
        infoboxService.set_infoBox();
        //on change route destroy infobox
        $scope.$on('$routeChangeStart', function(event, next, current) {
            console.log(JSON.stringify(next.$$route, null, 4));
            infoboxService.destroy_infoBox();
        });

        //-------------------------------------------------------------------------------------------------------------------------------------------------------------------

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
                    $scope.clientdata.selectedPlaceId = parseInt(msg.result);

                    // rest new insert place inputs
                    $scope.new_places = {}
                } else {
                    alert('Mesto koje ste uneli u polje već postoji u bazi podataka');
                }
            }).error(function(error){
                console.log(error);
            });

        };

        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.editClient = function(){



           // console.log($scope.clientdata); return false;
            $('.ajax_load_visibility').css('visibility','visible');
            clientsFactory.updateClient($scope.clientdata).success(function(msg){
                $('.ajax_load_visibility').css('visibility','hidden');
                console.log(msg);
                if(msg.success===0){
                    alert('Uspešno ste izmenili podatke');
                }else {
                    if(msg.field==='jmbg'){
                        errorService.error_msg($('input[name="jmbg"]'), msg.error_msg);
                    }
                }

            }).error(function(error){
                console.log(error);
            });



        };
    };
    editClientController.$inject = ['$scope', '$filter', '$routeParams', 'infoboxService', 'placesFactory', 'wearehouseFactory', 'clientsFactory', 'errorService'];
    angular.module('_raiffisenApp').controller('editClientController', editClientController)
}());