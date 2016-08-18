(function(){

    var userController = function($scope, $filter, infoboxService, placesFactory, wearehouseFactory, usersFactory, errorService){
        $scope.userdata = {};
        $scope.Places = {};
        $scope.Warehouses = {};
        $scope.placeVisible = true;
        $scope.loadNewPlace = true;
        $scope.userdata.selectedPlaceId = 0;
        $scope.wearehouseVisibility = true;
        //New places
        $scope.loadNewPlace =  false; //hide load icon ng-hide
        $scope.new_places = {};

        //start infobox mesages
        infoboxService.set_infoBox();
        //on change route destroy infobox
        $scope.$on('$routeChangeStart', function(event, next, current) {
            console.log(JSON.stringify(next.$$route, null, 4));
            infoboxService.destroy_infoBox();
        });

        //all user roles
        $scope.roles = [
            {role:'Administrator',label:'Administrator'},
            {role:'Magacioner',label:'Magacioner'},
            {role:'Redovan korisnik',label:'Redovan korisnik'},
            {role:'Logistika',label:'Logistika'}
        ];

        //get all places from database
        placesFactory.getPlaces().success(function(msg){
            $scope.Places = msg;
            console.log($scope.Places);
        }).error(function(error){
            console.log(error);
        });

        //get all wearehouses from database
        wearehouseFactory.getWearehouses().success(function(msg){
            $scope.Warehouses = msg;
            console.log($scope.Warehouses);
        }).error(function(error){
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

        $scope.addNewUser = function(){
            console.log($scope.userdata);
            if(!$scope.userdata.hasOwnProperty('name') || $scope.userdata.name==="" || typeof $scope.userdata.name === 'undefined'){
                errorService.error_msg($('input[name="name"]'), "Polje za unos imena je obavezno"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('surname') || $scope.userdata.surname==="" || typeof $scope.userdata.surname === 'undefined'){
                errorService.error_msg($('input[name="surname"]'), "Polje za unos prezimena je obavezno"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('address') || $scope.userdata.address==="" || typeof $scope.userdata.address === 'undefined'){
                errorService.error_msg($('input[name="address"]'), "Polje za unos adrese je obavezno"); return false;
            }
            if(!$scope.userdata.hasOwnProperty('selectedPlaceId') ||  $scope.userdata.selectedPlaceId===0 ||  $scope.userdata.selectedPlaceId===null || typeof $scope.userdata.selectedPlaceId === 'undefined'){
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
            usersFactory.insertUser($scope.userdata).success(function(msg){
                $('.ajax_load_visibility').css('visibility','hidden');
                console.log(msg);
                if(msg.success===0){
                    $scope.$broadcast('update_user_table');
                    $scope.formReset();
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

        $scope.formReset = function(){
            $scope.userdata ={};
        };

    };



    userController.$inject = ['$scope', '$filter', 'infoboxService', 'placesFactory', 'wearehouseFactory', 'usersFactory', 'errorService'];
    angular.module('_raiffisenApp').controller('userController', userController);



    var userList = function( DTOptionsBuilder, DTColumnBuilder, usersFactory, $scope, $resource, $compile ) {
        var vm = this;
        vm.changeUserActive = changeUserActive;
       // vm.stateChange = stateChange;
        vm.rowNum = 0;
        $scope.$on("update_user_table", function(event) {
            reloadData();
        });

        vm.reloadData = reloadData;
        vm.dtInstance = {};
        vm.users = {};
        vm.dtOptions = DTOptionsBuilder
            .fromSource('user_api/get_users')
            .withPaginationType('full_numbers')
            .withBootstrap()
            .withOption('createdRow', createdRow)
            .withColumnFilter({
                sPlaceHolder: "head:before",
                aoColumns: [{
                    type: 'number'
                }, {
                    type: 'text',
                    bRegex: true,
                    bSmart: true
                },
                    {
                        type: 'text',
                        bRegex: true,
                        bSmart: true
                    },
                    {
                        type: 'text',
                        bRegex: true,
                        bSmart: true
                    },
                    {
                        type: 'text',
                        bRegex: true,
                        bSmart: true
                    },
                    {
                        type: 'text',
                        bRegex: true,
                        bSmart: true
                    },
                    {
                        type: 'text',
                        bRegex: true,
                        bSmart: true
                    },

                    {
                        type: 'select',
                        bRegex: false,
                        values: ['Administrator', 'Magacioner', 'Redovan Korisnik']
                    },{
                        type: 'text',
                        bRegex: true,
                        bSmart: true
                    },]
            }).withLanguage({
                "sEmptyTable":     "<p>Nema raspoloživih podataka u tabeli</p>",
                "sInfo":           "Prikazujem _START_ do _END_ od totalno _TOTAL_ rezultata",
                "sInfoEmpty":      "Prikazujem 0 do 0 od totalno 0 redova",
                "sInfoFiltered":   "(Filtrirano od totalno _MAX_  rezultata)",
                "sInfoPostFix":    "",
                "sInfoThousands":  ",",
                "sLengthMenu":     "Prikazujem _MENU_ rezultate",
                "sLoadingRecords": "<i class='fa fa-cog fa-spin fa-3x load_new_place.ng-hide'></i> Load data...",
                "sLoadingPromise": "<i class='fa fa-cog fa-spin fa-3x load_new_place.ng-hide'></i> Load data...",
                "sProcessing":     "Procesuiram...",
                "sSearch":         "Traži:",
                "sZeroRecords":    "Nema podataka koji se poklapaju",
                "oPaginate": {
                    "sFirst":    "Prva",
                    "sLast":     "Zadnja",
                    "sNext":     "Sledeća",
                    "sPrevious": "Prethodna"
                },
                "oAria": {
                    "sSortAscending":  ": activate to sort column ascending",
                    "sSortDescending": ": activate to sort column descending"
                }
            });

        vm.dtColumns = [
            DTColumnBuilder.newColumn('row_number').withTitle('ID').withOption('width', '5%')/*.renderWith(actionsEditUser)/*.notVisible()*/,
            DTColumnBuilder.newColumn('user_name').withTitle('Ime i prezime'),
            DTColumnBuilder.newColumn('address').withTitle('Adresa'),
            DTColumnBuilder.newColumn('place').withTitle('Mesto/Naselje'),
            DTColumnBuilder.newColumn('supbrlk').withTitle('Br ličnekarte'),
            DTColumnBuilder.newColumn('jmbg').withTitle('JMBG'),
            DTColumnBuilder.newColumn('email').withTitle('Email'),
            DTColumnBuilder.newColumn('role').withTitle('Uloga'),
            DTColumnBuilder.newColumn('wearehouse').withTitle('Magacin'),
            DTColumnBuilder.newColumn(null).withTitle('Aktiviran').notSortable().withOption('width','10%').renderWith(actionsHtml).withClass('text-center')
        ];


        function reloadData() {
            var resetPaging = false;
            vm.dtInstance.changeData('user_api/get_users');
        }

        function changeUserActive(id, elem){
            console.log(id);
            var obj={};
            obj['user_id'] = id.user_id;
            obj['active'] = $('#'+elem).is(':checked')==true ? 'Y' : 'N';

            usersFactory.updateUserActivity(obj).success(function(msg){
            }).error(function(error){
                console.log(error);
            });
        }

        function createdRow(row, data, dataIndex) {
            // Recompiling so we can bind Angular directive to the DT
            $compile(angular.element(row).contents())($scope);
        }

        function actionsEditUser (data, type, full, meta) {


            vm.rowNum++;
            data.brnum = vm.rowNum;
            console.log(data);
            return vm.rowNum;
        }

        function actionsHtml (data, type, full, meta) {
           vm.users[data.user_id] = data;
            console.log(data);
            var Y = data.active === 'Y' ? 'checked="checked"' : '';

            return'<section class="smart-form " style="display:inline-block;">'+
                    '<label class="toggle" >' +
                        '<input type="checkbox" name="checkbox-toggle_'+data.user_id+'" id="checkbox-toggle_'+data.user_id+'" data-ng-click="userShowCase.changeUserActive(userShowCase.users[' + data.user_id + '], \'checkbox-toggle_'+data.user_id+'\')" '+Y+' >' +
                        '<i data-swchon-text="DA" data-swchoff-text="NE" ></i>' +
                    '</label>'+
                  '</section>  <a href="#/korisnik/' + data.user_id + '" class="btn-primary btn-xs" style="display:inline-block" ><i class="fa fa-pencil-square-o" title="Izmena podataka korisnika '+data.user_name+'"></i></a>';
        }
    };
    userList.$inject = ['DTOptionsBuilder', 'DTColumnBuilder', 'usersFactory', '$scope', '$resource', '$compile'];
    angular.module('_raiffisenApp').controller('userList', userList);


}());