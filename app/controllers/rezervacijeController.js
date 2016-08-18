(function(){
    var rezervacijeController = function($scope, $filter, prijemRepromaterijalPregledFactory, clientsFactory, mainService, errorService, errorService_second, rezervacijaFactory){
        $scope.insert_data = {};
        $scope.show_goods = false;
        $scope.goods_total = [];
        $scope.goods_ordered = [];
        $scope.clients = [];
        $scope.show_storniranje = false;
        $scope.storna_rezervacija = {}
        $scope.order_good_type = [
            {type_name:'Hemija',id:6},
            {type_name:'Djubrivo',id:9},
            {type_name:'Seme',id:7},
            {type_name:'Razna roba',id:15}
        ];
        $scope.session_info = window.session_info;
        //add datepicker to input field name datum
        $.datepicker.setDefaults( $.datepicker.regional[ "sr-SR" ] );
        $('input[name="datum"]').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd.mm.yy',
            onClose:function(){
                var name = $(this).attr('name');
                $scope.insert_data[name] = $(this).val();
            }
        });

        //storni container addd action dragable
        $('.storni_container').draggable({handle:'.storni_panel_header'})

        //add all wearehouses to select field
        prijemRepromaterijalPregledFactory.get_wearehouses().success(function(msg){
            if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false) {
                $scope.wearehouses = msg;
            } else {
                //not loged in
                window.location.href = mainService.domainURL();
            }
        }).error(function(error){
            console.log(error)
        });

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------

        var height = window.screen.availHeight - 380;
        $('.goods-wrapper').height(height);
        var height_half = $('.goods-wrapper').height() / 2;
        $('.goods-wrapper').css({'top':'50%', 'margin-top':'-'+height_half+'px'});
        $('.goods_body').height(height - 53);
        $( ".goods-wrapper" ).draggable({ handle: ".goods_head" });
        $scope.getGoods = function(){
            $('.get_goods').find('i').switchClass( "fa-shopping-basket", "fa-cog fa-spin", 0, "easeInOutQuad" );
            if(jQuery.isEmptyObject($scope.goods_total) === true){
                prijemRepromaterijalPregledFactory.get_all_goods()
                    .success(function(msg){
                        $scope.goods_total = msg;
                        $('.get_goods').find('i').switchClass( "fa-cog fa-spin", "fa-shopping-basket", 0, "easeInOutQuad" );
                        $scope.showGoodsPanel();
                    }).error(function(error){
                        console.log(error);
                    });
            } else {
                $('.get_goods').find('i').switchClass( "fa-cog fa-spin", "fa-shopping-basket", 0, "easeInOutQuad" );
                $scope.showGoodsPanel();
            }
        };

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.showGoodsPanel = function(){
            $scope.show_goods = !$scope.show_goods;
        };

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.orderGoods = function(element){
            console.log(element);
            var arr =
            {
                goods_id:element.goods_id,
                sort_of_goods_id: element.sort_of_goods_id,
                type_of_goods_id: element.type_of_goods_id,
                quantity:0,
                goods_name:element.goods_name,
                measurement_unit: element.measurement_unit
            };
            console.log(_);
            console.log(arr);
            obj = _.filter($scope.goods_ordered, function(item) { return item.goods_id == element.goods_id });
            console.log(obj);
            if( obj.length == 0 ){
                $scope.goods_ordered.push(arr);
            } else {
                console.log('postoji');
            };
        };

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.deleteFromOrder = function(obj){
            var myarr = $scope.goods_ordered, arr;
            arr = _.without(myarr, _.findWhere(myarr, {goods_id: obj.goods_id}));
            $scope.goods_ordered = arr;
            console.log(arr);
            //arr = _.without(arr, _.findWhere(arr, {id: 3}));
        };

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.getClient = function(){
            if(jQuery.isEmptyObject($scope.clients) === true){
                $('.get_client').find('i').switchClass( "fa-user-plus", "fa-cog fa-spin", 0, "easeInOutQuad" );
                clientsFactory.getClients()
                    .success(function(msg){
                        $scope.clients = msg;
                        $('.get_client').find('i').switchClass( "fa-cog fa-spin", "fa-user-plus", 0, "easeInOutQuad" );
                        $scope.showClientPanel();
                    }).error(function(){
                        console.log(error);
                    });
            } else {
                $scope.showClientPanel();
            }
        };

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.showClientPanel = function(){
            $scope.show_clients = !$scope.show_clients;
        };

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.setClient = function(id){
            var result = $filter('filter')($scope.clients , {client_id:id})[0];
            $scope.insert_data.client_id = result.client_id;
            $scope.insert_data.firm_name = result.firm_name;
            $scope.insert_data.client_name = result.client_name;
            $scope.insert_data.client_id = result.client_id;
            $scope.showClientPanel();
            console.log(result)
        };

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.firstMeasurement = function(){
            if(!$scope.insert_data.hasOwnProperty('wearehouse_id') ||  $scope.insert_data.wearehouse_id===0 ||  $scope.insert_data.wearehouse_id===null || typeof $scope.insert_data.wearehouse_id === 'undefined'){
                errorService.error_msg($('select[name="wearehouse"]'), "Morate izabrati magacin iz kojeg rezerviste robu!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('firm_name') ||  $scope.insert_data.firm_name===0 ||  $scope.insert_data.firm_name===null || typeof $scope.insert_data.firm_name === 'undefined'){
                errorService.error_msg($('input[name="firm_name"]'), "Morate kome rezervisete robu!"); return false;
            }

            if($scope.goods_ordered.length !== 0 ){
                if($scope.checkOrderSet() !== false){
                    $scope.insert_data.orders = $scope.goods_ordered;
                    $scope.setResult();
                }
            } else {
                errorService_second.error_msg($('.send_good_body'), "Morate dodati makar jedan proizvod da bi ste potvrdili ulaz robe!"); return false;
            }
        };

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.checkOrderSet = function(){
            var status = true;
            $.each($scope.goods_ordered, function(i, item){
                if(item.quantity <= 0){
                    errorService_second.error_msg($('.send_good_body'), "proizvodu "+item.goods_name+" niste dodelili kolicinu za rezervaciju!"); status=false; return false;
                }
            });
            return status;
        };

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.setResult = function(){
            console.log($scope.insert_data);
            $('.ajax_load_visibility').css({'visibility':'visible'});
            rezervacijaFactory.setReservation($scope.insert_data).success(function(msg){
                $('.ajax_load_visibility').css({'visibility':'hidden'});
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    console.log('logedin');
                    $scope.insert_data = {};                                //resetuje insert_data
                    $scope.goods_ordered = [];                              //resetuje goods_ordered
                    $scope.$broadcast('update_search_table_rezervacija');
                } else {
                    console.log('not logedin');
                    //logout
                    window.location.href = mainService.domainURL();
                }
            }).error(function(error){
                console.log(error);
            });
        };

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.stornirajRezervaciju = function(){
            var obj = {
                reservation_id: $scope.storna_rezervacija.reservation_id,
                napomena: $scope.napomena
            };
            if(!obj.hasOwnProperty('napomena') ||  obj.napomena===0 ||  obj.napomena===null || typeof obj.napomena === 'undefined'){
                errorService_second.error_msg($('textarea[name="napomena"]'), "Morate upisati razlog storniranja!"); return false;
            }
            $('.ajax_storno').css({'visibility':'visible'})
            rezervacijaFactory.storniraj_dokument(obj).success(function(msg){
                $('.ajax_storno').css({'visibility':'hidden'})
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $scope.show_storniranje = false;
                    $scope.napomena = '';
                    $scope.$broadcast('update_search_table_rezervacija');
                } else {
                    console.log('not logedin');
                    //logout
                    window.location.href = mainService.domainURL();
                }
            }).error(function(){
                console.log(error);
            });
        };




    };
    rezervacijeController.$inject = ['$scope', '$filter', 'prijemRepromaterijalPregledFactory', 'clientsFactory', 'mainService', 'errorService', 'errorService_second', 'rezervacijaFactory'];
    angular.module('_raiffisenApp').controller('rezervacijeController', rezervacijeController);

//***************************************************************************************************************************************************************

    var pregledRezervacijaTable = function( DTOptionsBuilder, DTColumnBuilder, usersFactory, $scope, $compile, mainService, rezervacijaFactory, $timeout) {
        var vm = this;
        $scope.show_prijem_repromaterijal=true;
       // vm.stornirajRezervaciju =stornirajRezervaciju;
        vm.predStorniranje = predStorniranje;
        vm.rowNum = 0;
        vm.reloadData = reloadData;
        vm.resetData = resetData;
        vm.dtInstance = {};
        vm.reservationResult = {};
        vm.result=null;
        vm.good_type=null;

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.$on("update_search_table_rezervacija", function(event) {
            reloadData();
        });

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.$on("reset_search_table_prijem", function(event) {
            //console.log($scope.$parent.link);
            resetData();
        });

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        vm.dtOptions = DTOptionsBuilder
            .fromSource(mainService.domainURL()+'reservation_api/get_reservation')
            .withPaginationType('full_numbers')
            .withBootstrap()
            .withOption('createdRow', createdRow)
            .withOption('aaSorting', [0, 'asc'])
            .withColumnFilter({
                sPlaceHolder: "head:before",
                aoColumns: [
                    {type: 'number'},
                    {type: 'text', bRegex: true, bSmart: true},
                    {type: 'text', bRegex: true, bSmart: true},
                    {type: 'text', bRegex: true, bSmart: true},
                    {type: 'select', bRegex: false, values: ['hemija', 'djubrivo', 'seme', 'razna roba']},
                    {type: 'text', bRegex: true, bSmart: true},
                    {type: 'text', bRegex: true, bSmart: true},
                    {type: 'text', bRegex: true, bSmart: true},
                    {type: 'select', bRegex: false, values: ['y', 'n']}
                ]
            }).withLanguage({
                "sEmptyTable":     "<p>Nema raspoloživih podataka u tabeli</p>",
                "sInfo":           "Prikazujem _START_ do _END_ od totalno _TOTAL_ rezultata",
                "sInfoEmpty":      "Prikazujem 0 do 0 od totalno 0 redova",
                "sInfoFiltered":   "(Filtrirano od totalno _MAX_  rezultata)",
                "sInfoPostFix":    "",
                "sInfoThousands":  ",",
                "sLengthMenu":     "Prikazi _MENU_ rezultata",
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

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        vm.dtColumns = [
            DTColumnBuilder.newColumn('reservation_id').withTitle('ID').withOption('width', '60px')/*.renderWith(actionsEditUser)*/ ,
            DTColumnBuilder.newColumn('datum').withTitle('Datum rezervacije').withOption('width', '80px'),
            DTColumnBuilder.newColumn('firm_name').withTitle('Firma/Gazdinstvo'),
            DTColumnBuilder.newColumn('wearehouse_name').withTitle('Magacin'),
            DTColumnBuilder.newColumn('goods_type').withTitle('Tip robe'),
            DTColumnBuilder.newColumn('goods_name').withTitle('Naziv robe'),
            DTColumnBuilder.newColumn('kolicina').withTitle('Kolicina').withClass('text-right').withOption('width','100px'),
            DTColumnBuilder.newColumn('measurement_unit').withTitle('Merna Jedinica').withClass('text-right').withOption('width','100px'),
            DTColumnBuilder.newColumn('realizovana').withTitle('Realizovana').withClass('text-center').withOption('width','80px'),
            DTColumnBuilder.newColumn(null).withTitle('Storniranje').notSortable().withOption('width','60px').renderWith(actionsHtml).withClass('text-center')
        ];

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        function reloadData() {
            console.log(vm.result);
            var resetPaging = false;
            vm.dtInstance.changeData(mainService.domainURL()+'reservation_api/get_reservation');
        }

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        function resetData() {
            var resetPaging = false;
            vm.dtInstance.changeData(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/empty_load');
        }

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        function predStorniranje(id){
            $scope.$parent.show_storniranje = true;
            $scope.$parent.storna_rezervacija = id;
            setTimeout(function() {
                $('#napomena').focus();
            }, 0);


        }

        /*function stornirajRezervaciju(id){
            //console.log(id.reservation_id);
            rezervacijaFactory.storniraj_dokument({reservation_id:id.reservation_id}).success(function(msg){
                reloadData();
                console.log(msg);
            }).error(function(){
                console.log(error);
            });
        }*/

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        function createdRow(row, data, dataIndex) {
            // Recompiling so we can bind Angular directive to the DT
            $compile(angular.element(row).contents())($scope);
        }

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        function actionsHtml (data, type, full, meta) {
            var t;
            vm.reservationResult[data.reservation_id] = data;
            if( $scope.$parent.session_info === 'Administrator' || $scope.$parent.session_info === undefined ){
                if(data.realizovana !== "y"){
                    t='<i class="fa fa fa-exclamation-triangle btn btn-danger btn-xs" style="margin-top:0px;margin-left:5px" title="Storniraj rezevaciju ' + data.reservation_id + '" data-ng-click="rezervacijaShowCase.predStorniranje(rezervacijaShowCase.reservationResult[' + data.reservation_id + '])"></i>';
                }else {
                    t='';
                }
            } else {
                t='';
            }
            return t;
        }

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

    };
    pregledRezervacijaTable.$inject = ['DTOptionsBuilder', 'DTColumnBuilder', 'usersFactory', '$scope', '$compile', 'mainService', 'rezervacijaFactory', '$timeout'];
    angular.module('_raiffisenApp').controller('pregledRezervacijaTable', pregledRezervacijaTable);

}());