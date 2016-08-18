(function(){
    var clientController = function($scope, $filter, infoboxService, placesFactory, wearehouseFactory, clientsFactory, errorService, $cookies){
        $scope.clientdata = {};
        $scope.new_places = {};
        $scope.placeVisible = true;
        $scope.loadNewPlace = false;
        $scope.formVisibility = $cookies.get('formVisibility') || 'open';

        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        $scope.changeFormVisibility = function(){
            $scope.formVisibility = $scope.formVisibility === 'open' ? 'close' : 'open';
            var expireDate = new Date();
            expireDate.setDate(expireDate.getDate() + 365);
            // Setting a cookie
            $cookies.put('formVisibility', $scope.formVisibility, {'expires': expireDate});
        };

        $scope.client_types = [
            {type:'fizicko',label:'Fizičko'},
            {type:'pravno',label:'Pravno'}
        ];

        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.sisem_pdv = [
            {text:'y',label:'Da'},
            {text:'n',label:'Ne'}
        ];

        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        //get all places from database
        placesFactory.getPlaces().success(function(msg){
            $scope.Places = msg;
            console.log($scope.Places);
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


        $scope.doVisible = function(){
            $scope.placeVisible = $scope.placeVisible===true ? false : true; //show, hide of new place panel on click
        };

        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.addNewClient = function(){

            if(!$scope.clientdata.hasOwnProperty('client_type') ||  $scope.clientdata.client_type===0 ||  $scope.clientdata.client_type===null || typeof $scope.clientdata.client_type === 'undefined'){
                errorService.error_msg($('select[name="client_type"]'), "Izaberite tip kupca/dobavljača "); return false;
            }
            if(!$scope.clientdata.hasOwnProperty('syspdv') ||  $scope.clientdata.syspdv===0 ||  $scope.clientdata.syspdv===null || typeof $scope.clientdata.syspdv === 'undefined'){
                errorService.error_msg($('select[name="syspdv"]'), "Izaberite da li je kupac/dobavljač u sistemu PDV-a "); return false;
            }
            if(!$scope.clientdata.hasOwnProperty('firmname') || $scope.clientdata.firmname==="" || typeof $scope.clientdata.firmname === 'undefined'){
                errorService.error_msg($('input[name="firmname"]'), "Polje za unos naziva imena firme/poljopivrednog preduzeća je obavezno"); return false;
            }
           /* if(!$scope.clientdata.hasOwnProperty('name') || $scope.clientdata.name==="" || typeof $scope.clientdata.name === 'undefined'){
                errorService.error_msg($('input[name="name"]'), "Polje za unos imena odgovornog lica je obavezno"); return false;
            }
            if(!$scope.clientdata.hasOwnProperty('surname') || $scope.clientdata.surname==="" || typeof $scope.clientdata.surname === 'undefined'){
                errorService.error_msg($('input[name="surname"]'), "Polje za unos prezimena odgovornog lica je obavezno"); return false;
            }
            if(!$scope.clientdata.hasOwnProperty('brlk') || $scope.clientdata.brlk==="" || typeof $scope.clientdata.brlk === 'undefined'){
                errorService.error_msg($('input[name="brlk"]'), "Polje za unos broja lične karte odgovornog lica je obavezno"); return false;
            }
            if(!$scope.clientdata.hasOwnProperty('sup') || $scope.clientdata.sup==="" || typeof $scope.clientdata.sup === 'undefined'){
                errorService.error_msg($('input[name="sup"]'), "Polje za unos supa je obavezno"); return false;
            }
            if(!$scope.clientdata.hasOwnProperty('jmbg') || $scope.clientdata.jmbg==="" || typeof $scope.clientdata.jmbg === 'undefined'){
                errorService.error_msg($('input[name="jmbg"]'), "Polje za unos JMBG odgovornog lica je obavezno"); return false;
            }*/
            if(!$scope.clientdata.hasOwnProperty('address') || $scope.clientdata.address==="" || typeof $scope.clientdata.address === 'undefined'){
                errorService.error_msg($('input[name="address"]'), "Polje za unos adrese je obavezno"); return false;
            }
             if(!$scope.clientdata.hasOwnProperty('selectedPlaceId') ||  $scope.clientdata.selectedPlaceId===0 ||  $scope.clientdata.selectedPlaceId===null || typeof $scope.clientdata.selectedPlaceId === 'undefined'){
                errorService.error_msg($('select[name="selectedPlaceId"]'), "Morate izabrati mesto kupca/dobavljača"); return false;
            }
            console.log($scope.clientdata);
            $('.ajax_load_visibility').css('visibility','visible');
            clientsFactory.insertClient($scope.clientdata).success(function(msg){
                $('.ajax_load_visibility').css('visibility','hidden');
                console.log(msg);
                if(msg.success===0){
                    $scope.$broadcast('update_client_table');
                    $scope.formReset();
                }else {
                    if(msg.field==='jmbg'){
                        errorService.error_msg($('input[name="jmbg"]'), msg.error_msg);
                    }
                }

            }).error(function(error){
                console.log(error);
            });



        };

        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.formReset = function(){
            $scope.clientdata ={};
        };

    };
    clientController.$inject = ['$scope', '$filter', 'infoboxService', 'placesFactory', 'wearehouseFactory', 'clientsFactory', 'errorService', '$cookies'];
    angular.module('_raiffisenApp').controller('clientController', clientController);


    var clientList = function( DTOptionsBuilder, DTColumnBuilder, clientsFactory, $scope, $resource, $compile ) {
        var vm = this;
        vm.changeUserActive = changeUserActive;
        // vm.stateChange = stateChange;
        vm.rowNum = 0;
        //remove green_plus clas on th in thead
        $('table').on('click', 'thead>tr>th', function(){
            $(this).parent().find('th:first-child').hasClass( "green_plus" )===true ? $(this).parent().find('th:first-child').removeClass("green_plus") : false;
        });
        $('table').on('click', 'tr.odd>td:first-child, tr.even>td:first-child', function(){
            $(this).hasClass( "green_plus" )===true ? $(this).switchClass( "green_plus", "red_minus", 0) : $(this).switchClass( "red_minus", "green_plus", 0);
        });
        $scope.$on("update_client_table", function(event) {
            reloadData();
        });

        vm.reloadData = reloadData;
        vm.dtInstance = {};
        vm.users = {};
        vm.dtOptions = DTOptionsBuilder
            .fromSource('client_api/get_clients')

           /* .withButtons([
                'copy',
                'excel',
                {
                    text: 'Some button',
                    key: '1',
                    action: function (e, dt, node, config) {
                        console.log(config);
                        //alert('Button activated');
                    }
                }
            ])*/
            .withPaginationType('full_numbers')
            .withBootstrap()
            .withOption('createdRow', createdRow)
            .withOption('responsive', true)
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
                        values: ['pravno', 'fizicko']
                    }]
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
            DTColumnBuilder.newColumn('row_number').withTitle('Br').withOption('width', '8%')/*.renderWith(function(){vm.rowNum++;return ' '+vm.rowNum;})*/.withClass('green_plus')/*.notVisible()*/,
            DTColumnBuilder.newColumn('client_cypher').withTitle('Šifra klijenta'),
            DTColumnBuilder.newColumn('firm_name').withTitle('Naziv firme'),
            DTColumnBuilder.newColumn('client_address').withTitle('Adresa'),
            DTColumnBuilder.newColumn('place').withTitle('Mesto/Naselje'),
            DTColumnBuilder.newColumn('client_tel').withTitle('Telefon'),
            DTColumnBuilder.newColumn('client_email').withTitle('Email'),
            DTColumnBuilder.newColumn('client_type').withTitle('Tip klijenta'),
            DTColumnBuilder.newColumn(null).withTitle('Izmena podataka').notSortable().renderWith(actionsHtml).withClass('text-center'),
            DTColumnBuilder.newColumn('client_name').withTitle('Odgovorno Lice').withClass('none'),
        ];


        function reloadData() {
            var resetPaging = false;
            vm.dtInstance.changeData('client_api/get_clients');
            //stateChange()
        }


        function changeUserActive(id, elem){
            console.log(id);
            var obj={};
            obj['user_id'] = id.user_id;
            obj['active'] = $('#'+elem).is(':checked')==true ? 'Y' : 'N';

            usersFactory.updateUser(obj).success(function(msg){
            }).error(function(error){
                console.log(error);
            });
        }

        function createdRow(row, data, dataIndex) {
            // Recompiling so we can bind Angular directive to the DT
            $compile(angular.element(row).contents())($scope);
        }

        function actionsHtml (data, type, full, meta) {
            //console.log(data);
            vm.users[data.client_id] = data;
            return '<a href="#/klijenti/kupac_dobavljac/' + data.client_id + '" class="btn-primary btn-xs" style="display:inline-block" ><i class="fa fa-pencil-square-o" title="Izmena podataka kupca/dobavljaca '+data.client_id+'"></i></a>';

           /* var Y = data.active === 'Y' ? 'checked="checked"' : '';

            return'<section class="smart-form ">'+
            '<label class="toggle" >' +
            '<input type="checkbox" name="checkbox-toggle_'+data.user_id+'" id="checkbox-toggle_'+data.user_id+'" data-ng-click="userShowCase.changeUserActive(userShowCase.users[' + data.user_id + '], \'checkbox-toggle_'+data.user_id+'\')" '+Y+' >' +
            '<i data-swchon-text="DA" data-swchoff-text="NE" ></i>' +
            '</label>'+
            '</section>';*/
        }
    };
    clientList.$inject = ['DTOptionsBuilder', 'DTColumnBuilder', 'clientsFactory', '$scope', '$resource', '$compile'];
    angular.module('_raiffisenApp').controller('clientList', clientList);

}());