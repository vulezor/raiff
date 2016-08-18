(function(){
    //---------------------------------------------------------------------------------------------------------------
    var weareHouseController = function($scope, $timeout, $filter, placesFactory, wearehouseFactory, googleMapService){

        var vm = this;
        $scope.new_place_name = '';
        $scope.new_post_number = '';
        $scope.placeVisible = true; // hide of new place panel
        $scope.loadNewPlace = true; // hide of load icon;
        $scope.Places = [];
        $scope.data = {};

        $scope.selectedPlaceId = 0;

        //scale models
        $scope.scale_models = [
            {model:'mx100',label:'MX100'},
            {model:'bm150',label:'BM150'},
            {model:'bm100',label:'BM100'},
            {model:'w2110',label:'W2110'}
        ];

        //ports inputs
        $scope.ports = [
            {port:'COM1'},
            {port:'COM2'},
            {port:'COM3'},
            {port:'COM4'},
            {port:'COM5'}
        ];

        //get all places from database
        placesFactory.getPlaces().success(function(msg){
            $scope.Places = msg;
            //console.log($scope.Places);
        }).error(function(error){
            console.log(error);
        });

        //initialize google map service and set start default long and lat
        googleMapService.initialize();
        $scope.longlat = googleMapService.getValues();
        $scope.data.longitude =  $scope.longlat.longitude;
        $scope.data.latitude =  $scope.longlat.latitude;

        //Updating new value of long and lat on marker drag on google maps
        $scope.$on( 'latlong.update', function( event ) {
            $scope.longlat = googleMapService.getValues();
            //console.log($scope.longlat);
            $scope.$apply(function(){
                $scope.data.longitude =  $scope.longlat.longitude;
                $scope.data.latitude =  $scope.longlat.latitude;
            });

        });


        //resolve visibility of new place insert container
        $scope.doVisible = function(){
            $scope.placeVisible = $scope.placeVisible===true ? false : true; //show, hide of new place panel on click
            $scope.loadNewPlace = true;                                      //hide load icon
        };


        //onclick to add new place
        $scope.insertNewPlace = function(){

            //filtering new_place_name value before insert
            $scope.new_place_name = $filter('serbian_replace')($scope.new_place_name); //Replace all serbian chars
            $scope.new_place_name = $filter('capitalize')($scope.new_place_name);      //Capitalize all first leter of every word

            //create new place object
            var new_place = {
                "place_name":$scope.new_place_name,
                "post_number": $scope.new_post_number
            };

            //show load icon ng-hide
            $scope.loadNewPlace = false;

            //saving new place into database
            placesFactory.insertPlace(new_place).success(function(msg){
                $scope.loadNewPlace = true; //hide load icon ng-hide
                $scope.placeVisible = true; //hide new place panel

                new_place.place_id = parseInt(msg); //add returning id in new_place object like place_id;
                $scope.Places.push(new_place);      //pushing new_place object into all places

                //selecting new place in select field
                $scope.selectedPlaceId = parseInt(msg);

                // rest new insert place inputs
                $scope.new_place_name = '';
                $scope.new_post_number = '';
            }).error(function(error){
                console.log(error);
            });
        };

        $scope.changedValue = function(){
            console.log($scope.selectedPlaceId);
        };

        $scope.addWarehouse = function() {
            $scope.data
            wearehouseFactory.insertWearehouse($scope.data).success(function(msg){
                $scope.$broadcast('update_parent_controller');
                $scope.formReset();
            }).error(function(error){
                console.log(error);
            });
        };

        $scope.formReset = function(){
            $scope.data ={};
        };
    };

    weareHouseController.$inject = ['$scope', '$timeout', '$filter', 'placesFactory', 'wearehouseFactory', 'googleMapService'];
    angular.module('_raiffisenApp')
        .controller('weareHouseController', weareHouseController);

//-----------------------------------------------------------------------------------------------------------------------------------------



   var WithAjaxCtrl = function( DTOptionsBuilder, DTColumnBuilder, wearehouseFactory, $scope, $resource, $compile ) {
        var vm = this;
       vm.rowNum= 0;
        vm.edit = edit;
        vm.stateChange = stateChange;

       $scope.$on("update_parent_controller", function(event) {
           reloadData();
       });

        vm.reloadData = reloadData;
        vm.dtInstance = {};
        vm.wearehouses = {};
        vm.dtOptions = DTOptionsBuilder/*.fromFnPromise(function() {
            vm.result = $resource('wearehouse_api/get_wearehouse').query().$promise;
            return  vm.result;
        })*/.fromSource('wearehouse_api/get_wearehouse')/*.withOption('stateSave', true)*/
            .withPaginationType('full_numbers').withBootstrap()
           .withOption('createdRow', createdRow)
            //.withOption('responsive', false)
            /*.withColVis()
            .withDOM('C<"clear">lfrtip')
            .withColVisOption('aiExclude', [5])*/
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
            DTColumnBuilder.newColumn(null).withTitle('ID').renderWith(renderNum)/*.notVisible()*/,
            DTColumnBuilder.newColumn('wearehouse_name').withTitle('Naziv magacina'),
            DTColumnBuilder.newColumn('wearehouse_address').withTitle('Adresa Magacina'),
            DTColumnBuilder.newColumn('place').withTitle('Mesto/Naselje'),
            DTColumnBuilder.newColumn('scale_type').withTitle('Model vage'),
            DTColumnBuilder.newColumn('scale_port').withTitle('Port vage'),
            DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable().renderWith(actionsHtml).withOption('width', '25%')
        ];

       function stateChange(){
           vm.dtOptions.withBootstrap();
           vm.dtColumns = [
               DTColumnBuilder.newColumn('wearehouse_id').withTitle('ID'),
               DTColumnBuilder.newColumn('wearehouse_name').withTitle('Naziv magacina'),
               DTColumnBuilder.newColumn('wearehouse_address').withTitle('Adresa Magacina').notVisible(),
               DTColumnBuilder.newColumn('place').withTitle('Mesto/Naselje').withClass('none'),
               DTColumnBuilder.newColumn('scale_type').withTitle('Model vage').withClass('none'),
               DTColumnBuilder.newColumn('scale_port').withTitle('Port vage').withClass('none'),
               DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable().renderWith(actionsHtml).withOption('width', '25%')
           ];
       }

       function renderNum(){vm.rowNum++; return vm.rowNum}

       function reloadData() {
           var resetPaging = false;
           vm.dtInstance.changeData('wearehouse_api/get_wearehouse');
           stateChange()
       }
       function callback(json) {
           var obj ={
               bruto_polje: "Zaklju",
               place: "fredol",
               place_id: 345,
               scale_port: "ddd",
               scale_type: "ddd",
               wearehouse_address: "dddd 10",
               wearehouse_id: 2,
               wearehouse_name: "ddd"
           };
           json.push(obj);
           console.log(json);
           return json;
       }

        function edit(id, val){
            console.log(id);
            console.log(val);
            var obj = {
                wearehouse_id:id.wearehouse_id,
                bruto_polje:val
            };
            wearehouseFactory.updateWearehouse(obj).success(function(msg){

            }).error(function(error){
                console.log(error);
            });
        }

        function createdRow(row, data, dataIndex) {
            // Recompiling so we can bind Angular directive to the DT
            $compile(angular.element(row).contents())($scope);
        }

        function actionsHtml (data, type, full, meta) {
            vm.wearehouses[data.wearehouse_id] = data;
            var otkljucano = data.bruto_polje === 'otkljucano' ? 'checked="checked"' : '';
            var zakljucano = data.bruto_polje === 'zakljucano' ? 'checked="checked"' : '';
           // console.log(otkljucano+ ', '+zakljucano)
            return'<div class="radio radio-primary radio-inline">'+
                '<input name="bruto_polje_'+data.wearehouse_id+'" type="radio"  id="inlineRadio1'+data.wearehouse_id+'" ng-click="showCase.edit( showCase.wearehouses[' + data.wearehouse_id + '], \'otkljucano\')"  '+otkljucano+' />'+
                '<label for="inlineRadio1'+data.wearehouse_id+'"> Otključano </label>'+
            '</div>'+
            '<div class="radio radio-primary radio-inline">'+
            '<input name="bruto_polje_'+data.wearehouse_id+'" type="radio" id="inlineRadio2'+data.wearehouse_id+'" ng-click="showCase.edit( showCase.wearehouses[' + data.wearehouse_id + '], \'zakljucano\')" '+zakljucano+' />'+
            '<label for="inlineRadio2'+data.wearehouse_id+'"> Zaključano </label>'+
            '</div>';
        }
    };
    WithAjaxCtrl.$inject = ['DTOptionsBuilder', 'DTColumnBuilder', 'wearehouseFactory', '$scope', '$resource', '$compile'];
    angular.module('_raiffisenApp').controller('WithAjaxCtrl', WithAjaxCtrl);
}());