(function(){

    var goodsController = function($scope, $filter, infoboxService, goodsFactory, errorService, $timeout, fileUploadService){
        $scope.measure_unit = {};
        $scope.goods_type = {};
        $scope.goods_class = {};
        $scope.goodsdata = {};
        $scope.new_measure_unit={};
        $scope.goodsVisible = true;
        $scope.loadNewMeasureUnit = false;
        $scope.goodsdata.measurement_unit_id = null;
        //start infobox mesages
        infoboxService.set_infoBox();

        //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        goodsFactory.getMeasureUnit().success(function(msg){
            $scope.measure_unit = msg;
            console.log($scope.measure_unit);
        }).error(function(error){
            console.log(error);
        });

        //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        goodsFactory.getGoodsClass().success(function(msg){
            $scope.goods_class = msg;
            console.log($scope.goods_class);
        }).error(function(error){
            console.log(error);
        });

        //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.showGoodsSort = function() {
            console.log($scope.goodsdata.sort_of_goods_id);
            if (!!$scope.goodsdata.sort_of_goods_id) {
                goodsFactory.getGoodsType($scope.goodsdata.sort_of_goods_id).success(function (msg) {
                    $scope.goods_type = msg;
                    console.log($scope.goods_type);
                }).error(function (error) {
                    console.log(error);
                });
            } else {
                $scope.goods_type={};
            }
        };

        //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.addNewMeasureUnit = function(){
            console.log($timeout);
            if(!$scope.new_measure_unit.hasOwnProperty('measurement_name') || $scope.new_measure_unit.measurement_name==="" || typeof $scope.new_measure_unit.measurement_name === 'undefined'){
                alert("Polje za unos naziva merne jedinice je obavezno");
                errorService.error_msg($('input[name="measurement_name"]'), "Polje za unos poštanskog broja je obavezno"); return false;
            }
            if(!$scope.new_measure_unit.hasOwnProperty('measurement_unit') || $scope.new_measure_unit.measurement_unit==="" || typeof $scope.new_measure_unit.measurement_unit === 'undefined'){
                alert("Polje za unos naziva oznake jedinice je obavezno");
                errorService.error_msg($('input[name="measurement_unit"]'), "Polje za unos naziva mesta/naselja je obavezno"); return false;
            }

            $scope.loadNewMeasureUnit =  true;

            goodsFactory.insertMeasureUnit($scope.new_measure_unit).success(function(msg){
                $scope.loadNewMeasureUnit = false; //hide load icon ng-hide
                if(msg.success === 0) {
                    $scope.goodsVisible = true; //hide new place panel

                    $scope.new_measure_unit.measurement_unit_id = parseInt(msg.result); //add returning id in new_place object like place_id;
                    $scope.measure_unit.push($scope.new_measure_unit);      //pushing new_place object into all places

                    //selecting new measure unit in select field
                   $scope.goodsdata.measurement_unit_id = msg.result;

                    // rest
                    $scope.new_measure_unit = {}
                } else {
                    alert(msg.error_msg);
                    $('input[name="'+msg.field+'"]').focus();
                }
            }).error(function(error){
                console.log(error);
            });
        };

        //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.addNewGood = function(){
            if(!$scope.goodsdata.hasOwnProperty('goods_name') || $scope.goodsdata.goods_name==="" || typeof $scope.goodsdata.goods_name === 'undefined'){
                errorService.error_msg($('input[name="goods_name"]'), "Polje za unos naziva robe je obavezno"); return false;
            }
            if(!$scope.goodsdata.hasOwnProperty('sort_of_goods_id') || $scope.goodsdata.sort_of_goods_id === "" || typeof $scope.goodsdata.sort_of_goods_id === 'undefined' || $scope.goodsdata.sort_of_goods_id === null) {
                errorService.error_msg($('select[name="sort_of_goods_id"]'), "Morate odabrati vrstu robe"); return false;
            }
            if(!$scope.goodsdata.hasOwnProperty('sort_of_type_id') || $scope.goodsdata.sort_of_type_id === "" || typeof $scope.goodsdata.sort_of_type_id === 'undefined' || $scope.goodsdata.sort_of_type_id === null) {
                errorService.error_msg($('select[name="sort_of_type_id"]'), "Morate dodeliti tip robe"); return false;
            }
            if(!$scope.goodsdata.hasOwnProperty('measurement_unit_id') || $scope.goodsdata.measurement_unit_id === "" || typeof $scope.goodsdata.measurement_unit_id === 'undefined' || $scope.goodsdata.measurement_unit_id === null) {
                errorService.error_msg($('select[name="measurement_unit_id"]'), "Morate dodeliti mernu jedinicu"); return false;
            }

            $('.ajax_load_visibility').css('visibility','visible');
            goodsFactory.insertGoods($scope.goodsdata).success(function(msg){
                $('.ajax_load_visibility').css('visibility','hidden');
                console.log(msg);
                if(msg.success===0){
                    $scope.$broadcast('update_goods_table');
                    $scope.formReset();
                }else {
                    if(msg.field==='goods_cypher'){
                        errorService.error_msg($('input[name="goods_cypher"]'), msg.error_msg);
                    }
                    if(msg.field==='goods_name'){
                        errorService.error_msg($('input[name="goods_name"]'), msg.error_msg);
                    }
                }
                /**/
            }).error(function(error){
                console.log(error);
            });
        };

        //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.doVisible = function(){
            $scope.goodsVisible = $scope.goodsVisible===true ? false : true; //show, hide of new place panel on click
        };

        $scope.formReset = function(){
            $scope.goodsdata ={};
        };

        //on change upload xlsx or xls file. On xls file expect max 3 columns wit 4-th who is optional.
        $scope.uploadFile = function(){
            var file = $scope.myFile;
            console.log('file is ' );
            console.dir(file);
            var uploadUrl = "goods_api/fileUpload";
            fileUploadService.uploadFileToUrl(file, uploadUrl).success(function(msg){
                if( msg.error === 1 ){
                    alert(msg.msg);
                }
                $('#myfile').val('');
                $scope.$broadcast('update_goods_table');
            }).error(function(error){
                console.log(error);
            });
        };

    };



    goodsController.$inject = ['$scope', '$filter', 'infoboxService', 'goodsFactory', 'errorService', '$timeout', 'fileUploadService'];
    angular.module('_raiffisenApp').controller('goodsController', goodsController);


    /*****************************************************************************************************************************************************/


    var goodsList = function( DTOptionsBuilder, DTColumnBuilder, goodsFactory, $scope, $resource, $compile ) {
        var vm = this;
        vm.goods = [];
      //  vm.changeUserActive = changeUserActive;
        vm.rowNum = 0;
        $scope.$on("update_goods_table", function(event) {
            reloadData();
        });
        vm.reloadData = reloadData;
        vm.dtInstance = {};
        vm.dtOptions = DTOptionsBuilder
            .fromSource('goods_api/get_goods')
            .withPaginationType('full_numbers')
            .withBootstrap()
            .withOption('createdRow', createdRow)
            .withColumnFilter({
                sPlaceHolder: "head:before",
                aoColumns: [
                    {
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
                    }
                ]
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
            DTColumnBuilder.newColumn('row_number').withTitle('Br.').withOption('width', '5%')/*.renderWith(actionsEditUser)/*.notVisible()*/,
            DTColumnBuilder.newColumn('goods_cypher').withTitle('Šifra robe'),
            DTColumnBuilder.newColumn('goods_name').withTitle('Naziv robe'),
            DTColumnBuilder.newColumn('goods_sort').withTitle('Vrsta robe'),
            DTColumnBuilder.newColumn('goods_type').withTitle('Tip robe'),
            DTColumnBuilder.newColumn('measurement_name').withTitle('Merna jedinica'),
            DTColumnBuilder.newColumn('measurement_unit').withTitle('Oznaka'),
            DTColumnBuilder.newColumn(null).withTitle('Izmena podataka').notSortable().renderWith(actionsHtml).withClass('text-center')
        ];


        function reloadData() {
            var resetPaging = false;
            vm.dtInstance.changeData('goods_api/get_goods');
        }


        function createdRow(row, data, dataIndex) {
            // Recompiling so we can bind Angular directive to the DT
            $compile(angular.element(row).contents())($scope);
        }

        function actionsHtml(data, type, full, meta){

                vm.goods[data.goods_id] = data;
                return '<a href="#/roba/izmena_podataka/' + data.goods_id + '" class="btn-primary btn-xs" style="display:inline-block" ><i class="fa fa-pencil-square-o" title="Izmena podataka robe '+data.goods_id+'"></i></a>';
        }

    };
    goodsList.$inject = ['DTOptionsBuilder', 'DTColumnBuilder', 'goodsFactory', '$scope', '$resource', '$compile'];
    angular.module('_raiffisenApp').controller('goodsList', goodsList);


}());