(function(){
    var pregledOtpremeRepromaterijalController = function($scope, infoboxService, otpremaRepromaterijalPregledFactory, errorService, errorService_second, mainService){
        $scope.search_data = {};
        $scope.get_search_good_type = {};
        $scope.get_search_good_name = {};
        $scope.get_search_good_wearehouses = {};
        $scope.get_search_good_client = {};
        $scope.total_result= {
            neto_total: 0.00,
            kolicina_total: 0.00
        };
        $scope.ifMereno = false;
        $scope.link = '';
        $scope.autent = $scope.$parent.login_data;
        $scope.last_measurement = {};
        $scope.ifKukuruz = false;
        $scope.ifZitarice = false;
        $scope.show_otprema_repromaterijal = false;
        $scope.session_info = window.session_info;
        //------------------------------------------------------------------------------------------------------

        $scope.show_storniranje = false;
        $scope.storna_otpremnica = {};


        $.datepicker.setDefaults( $.datepicker.regional[ "sr-SR" ] );
        $('input[name="datum_od"], input[name="datum_do"]').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd.mm.yy',
            onClose:function(){
                var name = $(this).attr('name');
                $scope.search_data[name] = $(this).val();
                /**/
            }
        });

        $scope.resetPretraga = function(){ //session_id=87582f36f65bd99c137cc5a51b3e7c9a&type_of_goods_id=1&goods_id=94&client_id=1&datum_od=01.01.2016&datum_do=01.01.2016
            $scope.search_data.type_of_goods_id = '';
            $scope.search_data.goods_id = '';
            $scope.search_data.client_id = '';
            $scope.search_data.datum_od = '';
            $scope.search_data.datum_do = '';
            $scope.$broadcast('reset_search_table_prijem');
        };

        //-------------------------------------------------------------------------------------------------------------


        otpremaRepromaterijalPregledFactory.get_search_good_type_admin().success(function(msg){
            $('.ajax_load_visibility').css({'visibility':'visible'});
            if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                $('.ajax_load_visibility').css({'visibility':'hidden'});
                console.log(msg);
                $scope.get_search_good_type = msg;
            } else {
                //not loged in
                $scope.$parent.logoutUser();
            }
        }).error(function(error){
            console.log(error);
        });


       $scope.getSearchGoodName = function(){
           otpremaRepromaterijalPregledFactory.get_search_good_name_admin($scope.search_data).success(function(msg){
                $('.ajax_load_visibility').css({'visibility':'visible'});
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $('.ajax_load_visibility').css({'visibility':'hidden'});
                    console.log(msg);
                    $scope.get_search_good_name = msg;
                } else {
                    //not loged in
                    $scope.$parent.logoutUser();
                }
            }).error(function(error){
                console.log(error);
            });
        };

        $scope.getSearchGoodWearehouses  = function(){
            otpremaRepromaterijalPregledFactory.get_search_good_wearehouses_admin($scope.search_data).success(function(msg){
                $('.ajax_load_visibility').css({'visibility':'visible'});
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $('.ajax_load_visibility').css({'visibility':'hidden'});
                    console.log(msg);
                    $scope.get_search_good_wearehouses = msg.wearehouses;
                    $scope.get_search_good_client = msg.clients;
                } else {
                    //not loged in
                    $scope.$parent.logoutUser();
                }
            }).error(function(error){
                console.log(error);
            });
        };

        $scope.getGoodClient = function(){
              otpremaRepromaterijalPregledFactory.get_search_good_client_admin($scope.search_data).success(function(msg){
                $('.ajax_load_visibility').css({'visibility':'visible'});
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $('.ajax_load_visibility').css({'visibility':'hidden'});
                    console.log(msg);
                    $scope.get_search_good_client = msg;
                } else {
                    //not loged in
                    $scope.$parent.logoutUser();
                }
            }).error(function(error){
                console.log(error);
            });
        };

        //-------------------------------------------------------------------------------------------------------------

        $scope.getSearchData = function(){
            if(!$scope.search_data.hasOwnProperty('type_of_goods_id') ||  $scope.search_data.type_of_goods_id===0 ||  $scope.search_data.type_of_goods_id===null || typeof $scope.search_data.type_of_goods_id === 'undefined'){
                errorService.error_msg($('select[name="type_of_goods_id"]'), "Morate izabrati tip repromaterijala !"); return false;
            }
            /*if(!$scope.search_data.hasOwnProperty('goods_id') ||  $scope.search_data.goods_id===0 ||  $scope.search_data.goods_id===null || typeof $scope.search_data.goods_id === 'undefined'){
                errorService.error_msg($('select[name="goods_id"]'), "Morate izabrati naziv repromaterijala !"); return false;
            }*/

            var link = '';
            for( var key in $scope.search_data){
                console.log(typeof $scope.search_data[key]);
                if($scope.search_data[key] !== '' && $scope.search_data[key] != null && $scope.search_data[key] != 'undefined'){

                    link += key+'='+$scope.search_data[key]+'&';
                }
            }
            $scope.link = link.slice(0, -1);
            console.log($scope.link);
            $scope.$broadcast('update_search_table_repromaterijal');
            if(!$scope.search_data.hasOwnProperty('goods_id') ||  $scope.search_data.goods_id===0 ||  $scope.search_data.goods_id===null || typeof $scope.search_data.goods_id === 'undefined'){
                $scope.total_result =[]; return false;
            }
            $scope.getTotalOfResult();
           // $scope.hideInputItems2($scope.search_data.type_of_goods_id)

        };

        $scope.getTotalOfResult = function(){
            otpremaRepromaterijalPregledFactory.get_search_prijem_total_admin($scope.search_data).success(function(msg){
                $('.ajax_load_visibility').css({'visibility':'visible'});
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $('.ajax_load_visibility').css({'visibility':'hidden'});
                    console.log(msg);
                    $scope.total_result = msg;
                } else {
                    //not loged in
                    $scope.$parent.logoutUser();
                }
            }).error(function(error){
                console.log(error);
            });
        };

       $scope.logout = function(){
            $scope.$parent.logoutUser();
        };
        //----------------------------------------------------------------------------------------------------------------------------------

       $scope.print = function(){
            var content = $('.print_prijem').html();
            $('.print_area').html(content);
            window.print();
        };

        //----------------------------------------------------------------------------------------------------------------------------------

        $scope.getExcell = function(){
            //window.location.href = mainService.domainURL()+'pregled_prijema_merkantile_api/getExcell'+$scope.link;
            window.open(mainService.domainURL()+'pregled_otpreme_repromaterijal_admin_api/getExcell?'+$scope.link,'_blank');
        };

        $scope.showOtpremaRepromaterijalContainer = function(){
            $scope.show_otprema_repromaterijal = !$scope.show_otprema_repromaterijal;
        };

        //storni container addd action dragable
        $('.storni_container').draggable({handle:'.storni_panel_header'});
        $scope.stornirajOtpremnicu = function(){
            var obj = {
                output_id: $scope.storna_otpremnica.output_id,
                napomena: $scope.napomena
            };
            //console.log(obj);return false;
            if(!obj.hasOwnProperty('napomena') ||  obj.napomena===0 ||  obj.napomena===null || typeof obj.napomena === 'undefined'){
                errorService_second.error_msg($('textarea[name="napomena"]'), "Morate upisati razlog storniranja!"); return false;
            }
            $('.ajax_storno').css({'visibility':'visible'})
            otpremaRepromaterijalPregledFactory.storniraj_dokument(obj).success(function(msg){
                $('.ajax_storno').css({'visibility':'hidden'})
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $scope.show_storniranje = false;
                    $scope.napomena = '';
                    $scope.$broadcast('update_search_table_repromaterijal');
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
    pregledOtpremeRepromaterijalController.$inject = ['$scope', 'infoboxService', 'otpremaRepromaterijalPregledFactory', 'errorService', 'errorService_second', 'mainService'];
    angular.module('_raiffisenApp').controller('pregledOtpremeRepromaterijalController', pregledOtpremeRepromaterijalController);

    //***************************************************************************************************************************************************************

     var pregledOtpremeRepromaterijalTable = function( DTOptionsBuilder, DTColumnBuilder, usersFactory, $scope, $timeout, $resource, $compile, mainService, otpremaRepromaterijalPregledFactory, errorService) {
        var vm = this;
        vm.printOtpremnica = printOtpremnica;
        vm.predStorniranje = predStorniranje;
        // vm.stateChange = stateChange;
        vm.rowNum = 0;
        $scope.$on("update_search_table_repromaterijal", function(event) {
            reloadData();
        });
        $scope.$on("reset_search_table_prijem", function(event) {
            //console.log($scope.$parent.link);
            resetData();
        });

        vm.reloadData = reloadData;
        vm.resetData = resetData;
        vm.uljariceColumns = uljariceColumns;
        vm.zitariceColumns = zitariceColumns;
        vm.kukuruzColumns = kukuruzColumns;
        vm.dtInstance = {};
        vm.otpremaResult = {};
        vm.result=null;
        vm.good_type=null;
        ;
        vm.dtOptions = DTOptionsBuilder
            .fromSource(mainService.domainURL()+'pregled_otpreme_repromaterijal_admin_api/empty_load')
            //.withDataProp('data.inner')
            .withPaginationType('full_numbers')
            .withBootstrap()
            .withOption('createdRow', createdRow)
            .withOption('aaSorting', [2, 'asc'])
            .withColumnFilter({
                sPlaceHolder: "head:before",
                aoColumns: [{
                    type: 'number'
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
                        type: 'text',
                        bRegex: true,
                        bSmart: true
                    }
                    ,
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

        vm.dtColumns = [
            DTColumnBuilder.newColumn('document_br').withTitle('Doc.br').withOption('width', '60px')/*.renderWith(actionsEditUser)*/ ,
            DTColumnBuilder.newColumn('date').withTitle('Datum prijema').withOption('width', '80px'),
            DTColumnBuilder.newColumn('firm_name').withTitle('Firma/Gazdinstvo'),
            DTColumnBuilder.newColumn('wearehouse_name').withTitle('Magacin'),
            DTColumnBuilder.newColumn('goods_name').withTitle('Naziv robe'),
            DTColumnBuilder.newColumn('vozac').withTitle('Vozač'),
            DTColumnBuilder.newColumn('neto').withTitle('Neto').withClass('text-right').withOption('width','100px'),
            DTColumnBuilder.newColumn('kolicina').withTitle('Kolicina').withClass('text-right').withOption('width','100px'),
            DTColumnBuilder.newColumn('merna_jedinica').withTitle('Merna jedinica').withClass('text-right').withOption('width','100px'),
            DTColumnBuilder.newColumn(null).withTitle('Otpremnica').notSortable().withOption('width','60px').renderWith(actionsHtml).withClass('text-center')
        ];



        function reloadData() {
            console.log(vm.result);
            var resetPaging = false;
            vm.dtInstance.changeData(mainService.domainURL()+'pregled_otpreme_repromaterijal_admin_api/get_search_prijem/?'+$scope.$parent.link);
        }

        function resetData() {
            var resetPaging = false;
            vm.dtInstance.changeData(mainService.domainURL()+'pregled_otpreme_repromaterijal_admin_api/empty_load');
        }

        function uljariceColumns() {
            vm.dtColumns = [
                DTColumnBuilder.newColumn('document_br').withTitle('Doc.br').withOption('width', '60px'),
                DTColumnBuilder.newColumn('date').withTitle('Datum prijema').withOption('width', '80px'),
                DTColumnBuilder.newColumn('firm_name').withTitle('Firma/Gazdinstvo'),
                DTColumnBuilder.newColumn('goods_name').withTitle('Naziv robe'),
                DTColumnBuilder.newColumn('vozac').withTitle('Vozač'),
                DTColumnBuilder.newColumn('neto').withTitle('Neto').withClass('text-right').withOption('width','60px'),
                DTColumnBuilder.newColumn('vlaga').withTitle('Vlaga').withClass('text-right').withOption('width','45px'),
                DTColumnBuilder.newColumn('primese').withTitle('Primese').withClass('text-right').withOption('width','45px'),
                DTColumnBuilder.newColumn('hektolitar').withTitle('Hektolitar').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('lom').withTitle('Lom').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('defekt').withTitle('Defekt').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('srps').withTitle('Srps').withClass('text-right').withOption('width','60px'),
                DTColumnBuilder.newColumn('trosak_susenja').withTitle('Trosak Susenja').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('suvo_zrno').withTitle('Suvo Zrno').withClass('text-right').withOption('width','60px').notVisible(),
                DTColumnBuilder.newColumn(null).withTitle('Prijemnica').notSortable().withOption('width','60px').renderWith(actionsHtml).withClass('text-center')
            ];
        }

        function zitariceColumns() {
            vm.dtColumns = [
                DTColumnBuilder.newColumn('document_br').withTitle('Doc.br').withOption('width', '60px'),
                DTColumnBuilder.newColumn('date').withTitle('Datum prijema').withOption('width', '80px'),
                DTColumnBuilder.newColumn('firm_name').withTitle('Firma/Gazdinstvo'),
                DTColumnBuilder.newColumn('goods_name').withTitle('Naziv robe'),
                DTColumnBuilder.newColumn('vozac').withTitle('Vozač'),
                DTColumnBuilder.newColumn('neto').withTitle('Neto').withClass('text-right').withOption('width','60px'),
                DTColumnBuilder.newColumn('vlaga').withTitle('Vlaga').withClass('text-right').withOption('width','45px'),
                DTColumnBuilder.newColumn('primese').withTitle('Primese').withClass('text-right').withOption('width','45px'),
                DTColumnBuilder.newColumn('hektolitar').withTitle('Hektolitar').withClass('text-right').withOption('width','45px'),
                DTColumnBuilder.newColumn('lom').withTitle('Lom').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('defekt').withTitle('Defekt').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('srps').withTitle('Srps').withClass('text-right').withOption('width','60px'),
                DTColumnBuilder.newColumn('trosak_susenja').withTitle('Trosak Susenja').withClass('text-right').withOption('width','45px'),
                DTColumnBuilder.newColumn('suvo_zrno').withTitle('Suvo Zrno').withClass('text-right').withOption('width','60px'),
                DTColumnBuilder.newColumn(null).withTitle('Prijemnica').notSortable().withOption('width','60px').renderWith(actionsHtml).withClass('text-center')
            ];
        }

        function bezObracunaColumns() {
            vm.dtColumns = [
                DTColumnBuilder.newColumn('document_br').withTitle('Doc.br').withOption('width', '60px'),
                DTColumnBuilder.newColumn('date').withTitle('Datum prijema').withOption('width', '80px'),
                DTColumnBuilder.newColumn('firm_name').withTitle('Firma/Gazdinstvo'),
                DTColumnBuilder.newColumn('goods_name').withTitle('Naziv robe'),
                DTColumnBuilder.newColumn('vozac').withTitle('Vozač'),
                DTColumnBuilder.newColumn('neto').withTitle('Neto').withClass('text-right').withOption('width','60px'),
                DTColumnBuilder.newColumn('vlaga').withTitle('Vlaga').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('primese').withTitle('Primese').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('hektolitar').withTitle('Hektolitar').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('lom').withTitle('Lom').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('defekt').withTitle('Defekt').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('srps').withTitle('Srps').withClass('text-right').withOption('width','60px').notVisible(),
                DTColumnBuilder.newColumn('trosak_susenja').withTitle('Trosak Susenja').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('suvo_zrno').withTitle('Suvo Zrno').withClass('text-right').withOption('width','60px').notVisible(),
                DTColumnBuilder.newColumn(null).withTitle('Prijemnica').notSortable().withOption('width','60px').renderWith(actionsHtml).withClass('text-center')
            ];
        }


        function kukuruzColumns() {
            vm.dtColumns = [
                DTColumnBuilder.newColumn('document_br').withTitle('Doc.br').withOption('width', '60px'),
                DTColumnBuilder.newColumn('date').withTitle('Datum prijema').withOption('width', '80px'),
                DTColumnBuilder.newColumn('firm_name').withTitle('Firma/Gazdinstvo'),
                DTColumnBuilder.newColumn('goods_name').withTitle('Naziv robe'),
                DTColumnBuilder.newColumn('vozac').withTitle('Vozač'),
                DTColumnBuilder.newColumn('neto').withTitle('Neto').withClass('text-right').withOption('width','60px'),
                DTColumnBuilder.newColumn('vlaga').withTitle('Vlaga').withClass('text-right').withOption('width','45px'),
                DTColumnBuilder.newColumn('primese').withTitle('Primese').withClass('text-right').withOption('width','45px'),
                DTColumnBuilder.newColumn('hektolitar').withTitle('Hektolitar').withClass('text-right').withOption('width','45px').notVisible(),
                DTColumnBuilder.newColumn('lom').withTitle('Lom').withClass('text-right').withOption('width','45px'),
                DTColumnBuilder.newColumn('defekt').withTitle('Defekt').withClass('text-right').withOption('width','45px'),
                DTColumnBuilder.newColumn('srps').withTitle('Srps').withClass('text-right').withOption('width','60px'),
                DTColumnBuilder.newColumn('trosak_susenja').withTitle('Trosak Susenja').withClass('text-right').withOption('width','45px'),
                DTColumnBuilder.newColumn('suvo_zrno').withTitle('Suvo Zrno').withClass('text-right').withOption('width','60px'),
                DTColumnBuilder.newColumn(null).withTitle('Prijemnica').notSortable().withOption('width','60px').renderWith(actionsHtml).withClass('text-center')
            ];
        }

        function printOtpremnica(id){
            console.log(id);
            /*$scope.$parent.last_measurement = id;
             $scope.$parent.hideInputItems(id.goods_type);*/
            /*window.setTimeout(function(){$scope.print();}, '200');*/
            otpremaRepromaterijalPregledFactory.getOtprema({'output_id':id.output_id}).success(function(msg){
                console.log(msg);
                if($.isEmptyObject(msg)===false){
                    if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                        $scope.$parent.last_measurement = msg;
                        if(msg.outputs[0].bruto === "0.000"){
                            $scope.$parent.ifMereno = true;
                            $timeout(function(){$scope.$parent.print()}, 200);
                        } else {
                            $scope.$parent.ifMereno = false;

                            $timeout(function(){$scope.$parent.print()}, 200);
                        }
                    } else {
                        $scope.$parent.logout();
                    }
                } else {

                }
            }).error(function(error){
                console.log(error)
            });

        }

        /*function stornirajOtpremnicu(id){
            console.log(id);
            otpremaRepromaterijalPregledFactory.storniraj_dokument(id).success(function(msg){
                reloadData();
                console.log(msg);
            }).error(function(){
                console.log(error);
            });
        }*/

         function predStorniranje(id){
             //console.log(id);return false;
             $scope.$parent.show_storniranje = true;
             $scope.$parent.storna_otpremnica = id;
             setTimeout(function() {
                 $('#napomena').focus();
             }, 0);
         }

        function createdRow(row, data, dataIndex) {
            // Recompiling so we can bind Angular directive to the DT
            $compile(angular.element(row).contents())($scope);
        }

        function actionsHtml (data, type, full, meta) {
            vm.otpremaResult[data.output_id] = data;
            var t = '  <i class="fa fa fa-print btn btn-primary btn-xs" style="margin-top:0px" title="Otpremnica' + data.output_id + '" data-ng-click="otpremaShowCase.printOtpremnica(otpremaShowCase.otpremaResult[' + data.output_id + '])"></i>';
            if( $scope.$parent.session_info === 'Administrator' || $scope.$parent.session_info === undefined ){
                t +='<i class="fa fa fa-exclamation-triangle btn btn-danger btn-xs" style="margin-top:0px;margin-left:5px" title="Storniraj otpremnicu ' + data.output_id + '" data-ng-click="otpremaShowCase.predStorniranje(otpremaShowCase.otpremaResult[' + data.output_id + '])"></i>';
            }
            return t;
        }
    };
    pregledOtpremeRepromaterijalTable.$inject = ['DTOptionsBuilder', 'DTColumnBuilder', 'usersFactory', '$scope', '$timeout','$resource', '$compile', 'mainService', 'otpremaRepromaterijalPregledFactory', 'errorService'];
    angular.module('_raiffisenApp').controller('pregledOtpremeRepromaterijalTable', pregledOtpremeRepromaterijalTable);


    /************************************************* IZLAZ REPROMATERIJAL CONTROLLER *************************************************************************************************************/

    var otpremaRepromaterijalController = function($scope, mainService, _, $filter, otpremaRepromaterijalPregledFactory, errorService, errorService_second, clientsFactory){
        $scope.insert_data = {};
        $scope.goods_type = {};
        $scope.goods_name = {};
        $scope.clients = [];
        $scope.show_clients = false;
        $scope.show_goods = false;
        $scope.session_id = null;
        $scope.ifMereno = false;
        $scope.goods_total = [];
        $scope.goods_ordered= [];
        $scope.last_measurement = {};
        $scope.sel_good_type= null;
        $scope.goods_name = null;
        $scope.wearehouses = [];
        //------------------------------------------------------------------------------------------------------

        $scope.order_good_type = [
            {type_name:'Hemija',id:6},
            {type_name:'Djubrivo',id:9},
            {type_name:'Seme',id:7},
            {type_name:'Razna roba',id:15}
        ];


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

        //------------------------------------------------------------------------------------------------------

        //dovload spisak magacina
        otpremaRepromaterijalPregledFactory.get_wearehouses().success(function(msg){
            if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false) {
                $scope.wearehouses = msg;
            } else {
                //not loged in
                window.location.href = mainService.domainURL();
            }
        }).error(function(error){
            console.log(error)
        });

        //----------------------------------------------------------------------------------------------------------------------------
        var height = window.screen.availHeight - 300;
        $('.goods-wrapper').height(height);
        var height_half = $('.goods-wrapper').height() / 2;
        $('.goods-wrapper').css({'top':'50%', 'margin-top':'-'+height_half+'px'});
        $('.goods_body').height(height - 53);
        $( ".goods-wrapper" ).draggable({ handle: ".goods_head" });
        $scope.getGoods = function(){
            $('.get_goods').find('i').switchClass( "fa-shopping-basket", "fa-cog fa-spin", 0, "easeInOutQuad" );
            if(jQuery.isEmptyObject($scope.goods_total) === true){
                otpremaRepromaterijalPregledFactory.get_all_goods()
                    .success(function(msg){
                        console.log(msg);
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

        //---------------------------------------------------------------------------------------------------------


        $scope.showGoodsPanel = function(){
            $scope.show_goods = !$scope.show_goods;
        };

        //---------------------------------------------------------------------------------------------------------

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


        $scope.deleteFromOrder = function(obj){
            var myarr = $scope.goods_ordered, arr;
            arr = _.without(myarr, _.findWhere(myarr, {goods_id: obj.goods_id}));
            $scope.goods_ordered = arr;
            console.log(arr);
            //arr = _.without(arr, _.findWhere(arr, {id: 3}));
        };

        //---------------------------------------------------------------------------------------------------------

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
        //----------------------------------------------------------------------------------------------------------------------------

        $scope.showClientPanel = function(){
            $scope.show_clients = !$scope.show_clients;
        };





        //----------------------------------------------------------------------------------------------------------------------------

        $scope.setClient = function(id){
            var result = $filter('filter')($scope.clients , {client_id:id})[0];
            $scope.insert_data.client_id = result.client_id;
            $scope.insert_data.firm_name = result.firm_name;
            $scope.insert_data.client_name = result.client_name;
            $scope.insert_data.client_id = result.client_id;
            $scope.showClientPanel();
            console.log(result)
        }

        //----------------------------------------------------------------------------------------------------------------------

        $scope.firstMeasurement = function(){
            if(!$scope.insert_data.hasOwnProperty('wearehouse_id') ||  $scope.insert_data.wearehouse_id===0 ||  $scope.insert_data.wearehouse_id===null || typeof $scope.insert_data.wearehouse_id === 'undefined'){
                errorService.error_msg($('select[name="wearehouse"]'), "Morate izabrati magacin u kojem radite ulaz robe!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('datum') ||  $scope.insert_data.datum===0 ||  $scope.insert_data.datum===null || typeof $scope.insert_data.datum === 'undefined'){
                errorService.error_msg($('input[name="datum"]'), "Morate oynaciti datum ulaz!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('firm_name') ||  $scope.insert_data.firm_name===0 ||  $scope.insert_data.firm_name===null || typeof $scope.insert_data.firm_name === 'undefined'){
                errorService.error_msg($('input[name="firm_name"]'), "Morate izabrati dobavljača!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('driver_name') ||  $scope.insert_data.driver_name===0 ||  $scope.insert_data.driver_name===null || typeof $scope.insert_data.driver_name === 'undefined'){
                errorService.error_msg($('input[name="driver_name"]'), "Morate upisati ime vozača!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('driver_reg') ||  $scope.insert_data.driver_reg===0 ||  $scope.insert_data.driver_reg===null || typeof $scope.insert_data.driver_reg === 'undefined'){
                errorService.error_msg($('input[name="driver_reg"]'), "Morate upisati registarski broj vozila !"); return false;
            }


            if($scope.goods_ordered.length !== 0 ){
                console.log($scope.checkOrderSet());
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
                    errorService_second.error_msg($('.send_good_body'), "proizvodu "+item.goods_name+" niste dodelili kolicinu!"); status=false; return false;
                }
            });
            return status;
        };

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.setResult = function(){
           // console.log($scope.insert_data);return false;
            otpremaRepromaterijalPregledFactory.insertRepromaterijal($scope.insert_data).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    console.log('logedin');
                    $scope.insert_data = {};           //resetuje insert_data
                    $scope.goods_ordered = [];         //resetuje goods_ordere
                    // $scope.insert_data.session_id = $scope.session_id;
                    $('input[name="datum"]').val('');
                    $scope.selectLastMeasurement();
                } else {
                    console.log('not logedin');
                    //not loged in
                    window.location.href = mainService.domainURL();
                }
            }).error(function(error){
                console.log(error);
            });
        };

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.selectLastMeasurement = function(){
            otpremaRepromaterijalPregledFactory.selectLastInput().success(function(msg){
                console.log(msg);
                if($.isEmptyObject(msg)===false){
                    $scope.showInput = true;
                    $scope.last_measurement = msg;
                    $scope.$parent.last_measurement = msg;
                    if(msg.outputs[0].bruto === "0.000"){
                        $scope.ifMereno = true;
                        $scope.$parent.ifMereno = true;

                    } else {
                        $scope.ifMereno = false;
                        $scope.$parent.ifMereno = false;
                    }
                } else {

                }
            }).error(function(error){
                console.log(error)
            });
        };

        $scope.selectLastMeasurement();
    };
    otpremaRepromaterijalController.$inject = ['$scope', 'mainService', '_', '$filter', 'otpremaRepromaterijalPregledFactory','errorService', 'errorService_second', 'clientsFactory'];
    angular.module('_raiffisenApp').controller('otpremaRepromaterijalController', otpremaRepromaterijalController);

}());