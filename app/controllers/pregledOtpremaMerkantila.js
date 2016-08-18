(function(){
    var pregledOtpremaMerkantila = function($scope, otpremaMerkantilaPregledFactory, errorService, mainService){
        $scope.search_data = {};
        $scope.get_search_good_type = {};
        $scope.get_search_good_name = {};
        $scope.get_search_good_wearehouses = {};
        $scope.get_search_good_client = {};
        $scope.total_result= {
            neto_total: 0.00,
            ponder_vlage: 0.00,
            ponder_primesa: 0.00,
            ponder_hektolitra: 0.00,
            ponder_loma: 0.00,
            ponder_defekta: 0.00,
            srps_total: 0.00,
            trosak_susenja_total: 0.00,
            trosak_susenja_total: 0.00,
            suvo_zrno_total: 0.00
        };
        $('.otprema_merkantila_container').draggable({handle:'.otprema_merkantila_container_header'});
        $scope.show_otprema_merkantila = false;

        $scope.session_info = window.session_info;

        //varijable za storniranje
        $scope.show_storniranje = false          //visibility stornog kontejnera
        $scope.storna_otpremnica = "";
        $scope.napomena = "";

        $scope.hideInputItems = function(kultura){
            if(kultura==='kukuruz'){
                $scope.ifKukuruz = true;
                $scope.ifZitarice = false;
                $scope.ifSacma = true;
            } else if(kultura === 'psenica' || kultura === 'psenica tel-kel'){
                $scope.ifKukuruz = false;
                $scope.ifZitarice = true;
                $scope.ifSacma = true;
            } else if(kultura === 'jecam'){
                $scope.ifKukuruz = false;
                $scope.ifZitarice = true;
                $scope.ifSacma = true;
            }else if(kultura === 'sacma' || kultura === 'kukuruz tel-kel'){
                $scope.ifKukuruz = false;
                $scope.ifZitarice = false;
                $scope.ifSacma = false;
            }  else {
                $scope.ifKukuruz = false;
                $scope.ifZitarice = false;
            }
        }

        $scope.hideInputItems2 = function(kultura){
            console.log(kultura);
            if(kultura===1){
                $scope.ifKukuruz = true;
                $scope.ifZitarice = false;
                $scope.ifSacma = true;
            } else if(kultura === 2 || kultura === 16){
                $scope.ifKukuruz = false;
                $scope.ifZitarice = true;
                $scope.ifSacma = true;
            } else if(kultura === 5){
                $scope.ifKukuruz = false;
                $scope.ifZitarice = true;
                $scope.ifSacma = true;
            } else if(kultura === 13 || kultura === 11){
                $scope.ifKukuruz = false;
                $scope.ifZitarice = false;
                $scope.ifSacma = false;
            } else {
                $scope.ifKukuruz = false;
                $scope.ifZitarice = false;
            }
        }

        /**
         * jQuery date picker to set callendar
         */
        $.datepicker.setDefaults( $.datepicker.regional[ "sr-SR" ] );
        $('input[name="datum_od"], input[name="datum_do"]').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd.mm.yy',
            onClose:function(){
                var name = $(this).attr('name');
                $scope.search_data[name] = $(this).val();
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

        //init



        //-------------------------------------------------------------------------------------------------------------

        $scope.getSearchGoodType = function(){
            otpremaMerkantilaPregledFactory.get_search_good_type_admin($scope.search_data).success(function(msg){
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
        };

        //-------------------------------------------------------------------------------------------------------------

        $scope.getSearchGoodName = function(){
            otpremaMerkantilaPregledFactory.get_search_good_name($scope.search_data).success(function(msg){
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


        $scope.getGoodWearehouses = function(){
            otpremaMerkantilaPregledFactory.get_search_good_wearehouses_admin($scope.search_data).success(function(msg){
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
        //-------------------------------------------------------------------------------------------------------------

        $scope.getGoodClient = function(){
            otpremaMerkantilaPregledFactory.get_search_good_client($scope.search_data).success(function(msg){
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
                errorService.error_msg($('select[name="type_of_goods_id"]'), "Morate izabrati tip merkantile !"); return false;
            }
            if(!$scope.search_data.hasOwnProperty('goods_id') ||  $scope.search_data.goods_id===0 ||  $scope.search_data.goods_id===null || typeof $scope.search_data.goods_id === 'undefined'){
                errorService.error_msg($('select[name="goods_id"]'), "Morate izabrati naziv merkantile !"); return false;
            }

            var link = '';
            for( var key in $scope.search_data){
                console.log(typeof $scope.search_data[key]);
                if($scope.search_data[key] !== '' && $scope.search_data[key] != null && $scope.search_data[key] != 'undefined'){

                    link += key+'='+$scope.search_data[key]+'&';
                }
            }
            $scope.link = link.slice(0, -1);
            console.log($scope.link);
            $scope.$broadcast('update_search_table');
            $scope.getTotalOfResult();
            $scope.hideInputItems2($scope.search_data.type_of_goods_id)

        };

        $scope.getTotalOfResult = function(){
            otpremaMerkantilaPregledFactory.get_search_prijem_total($scope.search_data).success(function(msg){
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

        $scope.print = function(){
            var content = $('.print_prijem').html();
            $('.print_area').html(content);
            window.print();
        };


        $scope.getExcell = function(){
            //window.location.href = mainService.domainURL()+'pregled_prijema_merkantile_api/getExcell'+$scope.link;
            window.open(mainService.domainURL()+'pregled_otpreme_merkantile_api/getExcellAdmin?'+$scope.link,'_blank');
        };

        //init
        $scope.getSearchGoodType();


        $scope.showOtpremaMerkantilaContainer = function(){
            $scope.show_otprema_merkantila = !$scope.show_otprema_merkantila;
        };

        $('.storni_container').draggable({handle:'.storni_panel_header'});
        $scope.stornirajOtpremnicu = function(){
            var obj = {
                output_id: $scope.storna_otpremnica.output_id,
                napomena: $scope.napomena
            };
            if(!obj.hasOwnProperty('napomena') ||  obj.napomena===0 ||  obj.napomena===null || typeof obj.napomena === 'undefined'){
                errorService_second.error_msg($('textarea[name="napomena"]'), "Morate upisati razlog storniranja!"); return false;
            }
            $('.ajax_storno').css({'visibility':'visible'})
            otpremaMerkantilaPregledFactory.storniraj_dokument(obj).success(function(msg){
                $('.ajax_storno').css({'visibility':'hidden'})
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $scope.show_storniranje = false;
                    $scope.napomena = '';
                    $scope.$broadcast('reload_search_table_otprema_merkantila');
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

    pregledOtpremaMerkantila.$inject = ['$scope', 'otpremaMerkantilaPregledFactory', 'errorService', 'mainService'];
    angular.module('_raiffisenApp').controller('pregledOtpremaMerkantila', pregledOtpremaMerkantila);

    //***************************************************************************************************************************************************************

    var pregledOtpremaTable = function( DTOptionsBuilder, DTColumnBuilder, usersFactory, $scope, $resource, $compile, mainService, otpremaMerkantilaPregledFactory, errorService) {
        var vm = this;
        vm.printOtpremnica = printOtpremnica;
        vm.predStorniranje = predStorniranje;
       // vm.stornirajOtpremnicu =stornirajOtpremnicu;
        // vm.stateChange = stateChange;
        vm.rowNum = 0;
        $scope.$on("update_search_table", function(event) {
            vm.good_type = $scope.$parent.search_data.type_of_goods_id;
            if( vm.good_type === 3 || vm.good_type === 4 || vm.good_type === 12){
                uljariceColumns();
                window.setTimeout(function(){
                    reloadData();
                }, '200');
            } else if(vm.good_type === 1){
                kukuruzColumns();
                window.setTimeout(function(){
                    reloadData();
                }, '200');
            } else if(vm.good_type === 2 || vm.good_type === 5 || vm.good_type === 16){
                zitariceColumns();
                window.setTimeout(function(){
                    reloadData();
                }, '200');
            }else if(vm.good_type === 13 || vm.good_type === 11){
                bezObracunaColumns();;
                window.setTimeout(function(){
                    reloadData();
                }, '200');
            }

        });


        $scope.$on("reload_search_table_otprema_merkantila", function(event) {
            //console.log($scope.$parent.link);
            reloadData();
        });

        $scope.$on("reset_search_table_otprema", function(event) {
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

            vm.result = 1;
            //reloadData();

        vm.dtOptions = DTOptionsBuilder
            .fromSource(mainService.domainURL()+'pregled_prijema_merkantile_api/empty_load')
            //.withDataProp('data.inner')
            .withPaginationType('full_numbers')
            .withBootstrap()
            .withOption('createdRow', createdRow)
            .withOption('aaSorting', [2, 'asc'])
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
            DTColumnBuilder.newColumn('document_br').withTitle('Doc.br').withOption('width', '60px')/*.renderWith(actionsEditUser)*/,
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
            DTColumnBuilder.newColumn(null).withTitle('Otpremnica').notSortable().withOption('width','60px').renderWith(actionsHtml).withClass('text-center')
        ];



        function reloadData() {
            console.log(vm.result);
            var resetPaging = false;
            vm.dtInstance.changeData(mainService.domainURL()+'pregled_otpreme_merkantile_api/get_search_otprema_admin/?'+$scope.$parent.link);
        }

        function resetData() {
            var resetPaging = false;
            vm.dtInstance.changeData(mainService.domainURL()+'pregled_otpreme_merkantile_api/empty_load');
        }

        function uljariceColumns() {
            vm.dtColumns = [
                DTColumnBuilder.newColumn('document_br').withTitle('Doc.br').withOption('width', '60px')/*.renderWith(actionsEditUser)*/,
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
                DTColumnBuilder.newColumn(null).withTitle('Otpremnica').notSortable().withOption('width','60px').renderWith(actionsHtml).withClass('text-center')
            ];
        }

        function zitariceColumns() {
            vm.dtColumns = [
                DTColumnBuilder.newColumn('document_br').withTitle('Doc.br').withOption('width', '60px')/*.renderWith(actionsEditUser)*/,
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
                DTColumnBuilder.newColumn(null).withTitle('Otpremnica').notSortable().withOption('width','60px').renderWith(actionsHtml).withClass('text-center')
            ];
        }

        function bezObracunaColumns() {
            vm.dtColumns = [
                DTColumnBuilder.newColumn('document_br').withTitle('Doc.br').withOption('width', '60px')/*.renderWith(actionsEditUser)*/,
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
                DTColumnBuilder.newColumn(null).withTitle('Otpremnica').notSortable().withOption('width','60px').renderWith(actionsHtml).withClass('text-center')
            ];
        }


        function kukuruzColumns() {
            vm.dtColumns = [
                DTColumnBuilder.newColumn('document_br').withTitle('Doc.br').withOption('width', '60px')/*.renderWith(actionsEditUser)*/,
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
                DTColumnBuilder.newColumn(null).withTitle('Otpremnica').notSortable().withOption('width','60px').renderWith(actionsHtml).withClass('text-center')
            ];
        }

        function printOtpremnica(id){
            console.log(id);
            $scope.$parent.last_measurement = id;
            $scope.$parent.hideInputItems(id.goods_type);
            window.setTimeout(function(){$scope.print();}, '200');


        }

        function predStorniranje(id){
            //console.log(id);return false;
            $scope.$parent.show_storniranje = true;
            $scope.$parent.storna_otpremnica = id;
            setTimeout(function() {
                $('#napomena').focus();
            }, 0);
        }
        /*function stornirajOtpremnicu(id){
            console.log(id);
            var txt;
            var r = confirm("Da li ste sigurni da želite da stornirate otpremnicu broj "+id.document_br+"!");
            if (r == true) {
                txt = "You pressed OK!";
            } else {
                return false;
            }
            otpremaMerkantilaPregledFactory.storniraj_dokument(id).success(function(msg){
                console.log(msg);
                reloadData()
            }).error(function(){
                console.log(error);
            });
        }*/

        function createdRow(row, data, dataIndex) {
            // Recompiling so we can bind Angular directive to the DT
            $compile(angular.element(row).contents())($scope);
        }

        function actionsHtml (data, type, full, meta) {
            console.log(data);
            vm.otpremaResult[data.output_id] = data;
            var t ='  <i class="fa fa fa-print btn btn-primary btn-xs" style="margin-top:0px" title="Stampaj otpremnicu" data-ng-click="otpremaShowCase.printOtpremnica(otpremaShowCase.otpremaResult[' + data.output_id + '])"></i>';
            if( $scope.$parent.session_info === 'Administrator' || $scope.$parent.session_info === undefined ){
                t +=' <i class="fa fa fa-exclamation-triangle btn btn-danger btn-xs" style="margin-top:0px" title="Storniranje otpremnice br.'+data.document_br+'" data-ng-click="otpremaShowCase.predStorniranje(otpremaShowCase.otpremaResult[' + data.output_id + '])"></i>';
            }
            return t
        }
    };
    pregledOtpremaTable.$inject = ['DTOptionsBuilder', 'DTColumnBuilder', 'usersFactory', '$scope', '$resource', '$compile', 'mainService', 'otpremaMerkantilaPregledFactory', 'errorService'];
    angular.module('_raiffisenApp').controller('pregledOtpremaTable', pregledOtpremaTable);



    /************************************************* IZLAZ MERKANTILA CONTROLLER *************************************************************************************************************/

    var otpremaMerkantilaController = function($scope, $filter, otpremaMerkantilaPregledFactory, mainService, clientsFactory, errorService){
        $scope.insert_data = {};
        $scope.wearehouses = [];
        $scope.goods_type = [];
        $scope.culture = {
            hektolitar:false,
            lom:false,
            defekt:false
        };
        $scope.goods_name = [];

        $scope.clients = [];
        $scope.first_measurement = [];
        $scope.show_clients = false;
        var result;

        $scope.session_id = null;
        $scope.showInput = $scope.showInput;
        $scope.last_measurement = {};
        $scope.ifKukuruz = true;
        $scope.ifZitarice = true;
        $scope.ifSacma = true;
        $scope.enableDays = [];
        $scope.otpremaSearch = {};



        $.datepicker.setDefaults( $.datepicker.regional[ "sr-SR" ] );
        $('input[name="datum"]').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd.mm.yy',
            onClose:function(){
                var name = $(this).attr('name');
                $scope.insert_data[name] = $(this).val();
                /**/
            }
        });

        //dovload spisak magacina
        otpremaMerkantilaPregledFactory.get_wearehouses().success(function(msg){
            if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false) {
                $scope.wearehouses = msg;
            } else {
                //not loged in
                window.location.href = mainService.domainURL();
            }
        }).error(function(error){
            console.log(error)
        });

        //dovnload tip merkantile
        otpremaMerkantilaPregledFactory.getGoodsType().success(function(msg){
            if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                console.log(msg);
                $scope.goods_type = msg;
            } else {
                //not loged in
                window.location.href = mainService.domainURL();
            }
        }).error(function(error){
            console.log(error);
        });


        //izaberi naziv proizvoda na osnovu selekta tipa proizvoda
        $scope.selectGoodsName = function(){
            otpremaMerkantilaPregledFactory.getGoodsName($scope.insert_data.type_of_goods_id).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $scope.goods_name = msg;
                    $scope.insert_data.goods_type = $("select[name='insert_type_of_goods_id'] option:selected").text();
                    if($scope.insert_data.type_of_goods_id===1){
                        $scope.culture.hektolitar=false;
                        $scope.culture.lom=true;
                        $scope.culture.defekt=true;
                        $scope.culture.vlaga=true;
                        $scope.culture.primese=true;
                    } else if($scope.insert_data.type_of_goods_id===2 || $scope.insert_data.type_of_goods_id===16){
                        $scope.culture.hektolitar=true;
                        $scope.culture.lom=false;
                        $scope.culture.defekt=false;
                        $scope.culture.vlaga=true;
                        $scope.culture.primese=true;
                    } else if($scope.insert_data.type_of_goods_id===5){
                        $scope.culture.hektolitar=true;
                        $scope.culture.lom=false;
                        $scope.culture.defekt=false;
                        $scope.culture.vlaga=true;
                        $scope.culture.primese=true;
                    } else if($scope.insert_data.type_of_goods_id===13 || $scope.insert_data.type_of_goods_id===11 || $scope.insert_data.type_of_goods_id===14 ){
                        $scope.culture.hektolitar=false;
                        $scope.culture.lom=false;
                        $scope.culture.defekt=false;
                        $scope.culture.vlaga=false;
                        $scope.culture.primese=false;
                    } else {
                        $scope.culture.hektolitar=false;
                        $scope.culture.lom=false;
                        $scope.culture.defekt=false;
                        $scope.culture.vlaga=true;
                        $scope.culture.primese=true;
                    }
                } else {
                    //not loged in
                    window.location.href = mainService.domainURL();
                }
            }).error(function(error){
                console.log(error);
            });
        }

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

        //---------------------------------------------------------------------------------------------------------

        $scope.showClientPanel = function(){
            $scope.show_clients = !$scope.show_clients;
        };

        //--------------------------------------------------------------------------------------------------------------
        $scope.setClient = function(id){
            var result = $filter('filter')($scope.clients , {client_id:id})[0];
            $scope.insert_data.client_id = result.client_id;
            $scope.insert_data.firm_name = result.firm_name;
            $scope.insert_data.client_name = result.client_name;
            $scope.insert_data.client_id = result.client_id;
            $scope.showClientPanel();
            console.log(result)
        };


        //--------------------------------------------------------------------------------------------------------------

        $scope.firstMeasurement = function(){


            if(!$scope.insert_data.hasOwnProperty('wearehouse_id') ||  $scope.insert_data.wearehouse_id===0 ||  $scope.insert_data.wearehouse_id===null || typeof $scope.insert_data.wearehouse_id === 'undefined'){
                errorService.error_msg($('select[name="wearehouse_id"]'), "Morate izabrati Magacin iz kojeg otpremate merkantilnu robu!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('datum') || $scope.insert_data.datum==="" || $scope.insert_data.datum===" " || typeof $scope.insert_data.datum === 'undefined'){
                errorService.error_msg($('input[name="datum"]'), "Morate selektovati datum otpreme!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('type_of_goods_id') ||  $scope.insert_data.type_of_goods_id===0 ||  $scope.insert_data.type_of_goods_id===null || typeof $scope.insert_data.type_of_goods_id === 'undefined'){
                errorService.error_msg($('select[name="insert_type_of_goods_id"]'), "Morate izabrati tip merkantilne robe!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('goods_id') ||  $scope.insert_data.goods_id===0 ||  $scope.insert_data.goods_id===null || typeof $scope.insert_data.goods_id === 'undefined'){
                errorService.error_msg($('select[name="goods_id1"]'), "Morate izabrati naziv merkantilne robe!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('firm_name') || $scope.insert_data.firm_name==="" || typeof $scope.insert_data.firm_name === 'undefined'){
                errorService.error_msg($('input[name="firm_name"]'), "Morate selektovati kupca!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('driver_name') || $scope.insert_data.driver_name==="" || typeof $scope.insert_data.driver_name === 'undefined'){
                errorService.error_msg($('input[name="driver_name"]'), "Morate upisati ime i prezime vozača!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('driver_reg') || $scope.insert_data.driver_reg==="" || typeof $scope.insert_data.driver_reg === 'undefined'){
                errorService.error_msg($('input[name="driver_reg"]'), "Morate upisati registraciju vozila!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('bruto') || $scope.insert_data.bruto==="" || typeof $scope.insert_data.bruto === 'undefined'){
                errorService.error_msg($('input[name="bruto"]'), "Morate uneti vrednost bruta!"); return false;
            }
            if($scope.insert_data.type_of_goods_id !==13 && $scope.insert_data.type_of_goods_id !==11){
                if(!$scope.insert_data.hasOwnProperty('vlaga') || $scope.insert_data.vlaga==="" || typeof $scope.insert_data.vlaga === 'undefined'){
                    errorService.error_msg($('input[name="vlaga"]'), "Morate uneti vrednost vlage!"); return false;
                }
                if(!$scope.insert_data.hasOwnProperty('primese') || $scope.insert_data.primese==="" || typeof $scope.insert_data.primese === 'undefined'){
                    errorService.error_msg($('input[name="primese"]'), "Morate uneti vrednost primesa!"); return false;
                }
            }
            if($scope.insert_data.type_of_goods_id===2 || $scope.insert_data.type_of_goods_id===5){
                if(!$scope.insert_data.hasOwnProperty('hektolitar') || $scope.insert_data.hektolitar==="" || typeof $scope.insert_data.hektolitar === 'undefined'){
                    errorService.error_msg($('input[name="hektolitar"]'), "Morate uneti vrednost hektolitra!"); return false;
                }
            }
            if($scope.insert_data.type_of_goods_id===1){
                if(!$scope.insert_data.hasOwnProperty('lom') || $scope.insert_data.lom==="" || typeof $scope.insert_data.lom === 'undefined'){
                    errorService.error_msg($('input[name="lom"]'), "Morate uneti vrednost loma!"); return false;
                }
                if(!$scope.insert_data.hasOwnProperty('defekt') || $scope.insert_data.defekt==="" || typeof $scope.insert_data.defekt === 'undefined'){
                    errorService.error_msg($('input[name="defekt"]'), "Morate uneti vrednost defekta!"); return false;
                }
            }

            //--------------------------------------------------------------------------------------------------------------

            //console.log($scope.insert_data);
            $('.ajax_load_visibility').css('visibility','visible');
            otpremaMerkantilaPregledFactory.insertMerkantila($scope.insert_data).success(function(msg){//testSession
                $('.ajax_load_visibility').css('visibility','hidden');
                console.log(msg);
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    console.log('logedin');
                    $scope.insert_data ={};
                    $scope.insert_data.session_id = $scope.session_id;
                    $scope.selectLastMeasurement();
                    console.log($scope.session_id);
                } else {
                    console.log('not logedin');
                    window.location.href = mainService.domainURL();
                }
            }).error(function(error){
                console.log(error);
            });
        };

        //--------------------------------------------------------------------------------------------------------------

        $scope.selectLastMeasurement = function(){
            otpremaMerkantilaPregledFactory.selectLastOutput().success(function(msg){
                if($.isEmptyObject(msg)===false){
                    $scope.showInput = true;
                    $scope.last_measurement = msg[0];
                    $scope.hideInputItems($scope.last_measurement.goods_type);
                    $scope.$parent.last_measurement = msg[0];
                    $scope.$parent.hideInputItems(msg[0].goods_type);
                    // window.setTimeout(function(){$scope.print();}, '200');
                } else {

                }
            }).error(function(error){
                console.log(error)
            });
        };

        //--------------------------------------------------------------------------------------------------------------

        $scope.hideInputItems = function(kultura){
            if(kultura==='kukuruz'){
                $scope.ifKukuruz = true;
                $scope.ifZitarice = false;
                $scope.ifSacma = true;
                $scope.last_measurement.neto2 = $scope.last_measurement.neto - $scope.last_measurement.kalo_rastur;
                $scope.last_measurement.neto2 = $scope.last_measurement.neto2.toFixed(2);
            } else if(kultura === 'psenica' || kultura === 'psenica tel-kel'){
                $scope.ifKukuruz = false;
                $scope.ifZitarice = true;
                $scope.ifSacma = true;
            } else if(kultura === 'jecam'){
                $scope.ifKukuruz = false;
                $scope.ifZitarice = true;
                $scope.ifSacma = true;
            } else if(kultura === 'sacma' || kultura === 'kukuruz tel-kel'){
                $scope.ifKukuruz = false;
                $scope.ifZitarice = false;
                $scope.ifSacma = false;
            } else {
                $scope.ifKukuruz = false;
                $scope.ifZitarice = false;
            }
        };

        //---------------------------------------------------------------------------------------------------------

        $scope.selectLastMeasurement();

        //---------------------------------------------------------------------------------------------------------

        $scope.enableAllTheseDays = function(date) {
            var sdate = $.datepicker.formatDate( 'd-m-yy', date)

            if($.inArray(sdate, $scope.enableDays) != -1) {
                return [true,"","Zabeležen prijem"];
            }
            return [false,"","Nema beleženog prijema"];
        };

        //------------------------------------------------------------------------------------------------------------

        $scope.openSearchPanelState = true;
        $scope.gimePrijemPanel = function(){
            if($scope.openSearchPanelState === true){
                $scope.openSearchPanel();
                $scope.openSearchPanelState = false;
            }else{
                $scope.closePrijemPanel();
                $scope.openSearchPanelState = true;
            }
        };

        //------------------------------------------------------------------------------------------------------------


        $scope.openSearchPanel = function(){
            $('.otpremaSearch').find('.fa-cog').addClass('fa-spin');
            otpremaMerkantilaPregledFactory.enableDays().success(function(msg){
                $('.searchPanelPrijem').stop().animate({'margin-bottom':'0px'}, 800, 'easeInOutQuad', function(){
                    setTimeout(function() {
                        $('.otpremaSearch').find('.fa-cog').removeClass('fa-spin');
                    }, 800);
                });
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $scope.enableDays = [];
                    for(i=0;i<msg.length;i++){
                        $scope.enableDays.push(msg[i].datum);
                    }
                    $.datepicker.setDefaults( $.datepicker.regional[ "sr-SR" ] );
                    $('input[name="datumPrijema"]').datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'dd.mm.yy',
                        beforeShowDay: $scope.enableAllTheseDays,
                        onClose:function(){
                            $scope.$apply(function(){
                                $scope.getSearchType();
                            });
                        }
                    });
                    //console.log($scope.enableDays);
                } else {
                    //not loged in
                    window.location.href = mainService.domainURL();
                }
            }).error(function(){
                console.log('error');
            });
        };

        $scope.closePrijemPanel = function(){
            $('.otpremaSearch').find('.fa-cog').addClass('fa-spin');
            $('.searchPanelPrijem').animate({'margin-bottom':'-238px'}, 800, 'easeInOutQuad', function(){
                setTimeout(function() {
                    $('.otpremaSearch').find('.fa-cog').removeClass('fa-spin');
                }, 800);
            });
            console.log($scope.otpremaSearch);
            $('input[name="datumPrijema"]').datepicker('destroy');
        };

        //--------------------------------------------------------------------------------------------------------------------------------

        $scope.getSearchType = function(){
            $('.searchPanelHead').find('i').switchClass( "fa-search", "fa-cog fa-spin", 0, "easeInOutQuad" );
            $scope.otpremaSearch.output_type={}
            $scope.otpremaSearch.goods_names = {};
            $scope.otpremaSearch.outputs = {};
            otpremaMerkantilaPregledFactory.getSearchType($scope.otpremaSearch).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    console.log(msg);
                    $scope.otpremaSearch.output_type = msg;
                    $('.searchPanelHead').find('i').switchClass( "fa-cog fa-spin", "fa-search", 0, "easeInOutQuad" );
                } else {
                    //not loged in
                    $scope.$parent.logoutUser();
                }
            }).error(function(error){
                console.log(error);
            });
        };

        $scope.getSearchMerkantilaName = function(){
            $('.searchPanelHead').find('i').switchClass( "fa-search", "fa-cog fa-spin", 0, "easeInOutQuad" );
            otpremaMerkantilaPregledFactory.getSearchMerkantilaName($scope.otpremaSearch).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    console.log(msg);
                    $scope.otpremaSearch.goods_names = msg;
                    $('.searchPanelHead').find('i').switchClass( "fa-cog fa-spin", "fa-search", 0, "easeInOutQuad" );
                } else {
                    //not loged in
                    $scope.$parent.logoutUser();
                }
            }).error(function(error){
                console.log(error);
            });
        };

        $scope.getSearchOtpremnica = function(){
            $('.searchPanelHead').find('i').switchClass( "fa-search", "fa-cog fa-spin", 0, "easeInOutQuad" );
            otpremaMerkantilaPregledFactory.getSearchOtpremnica($scope.otpremaSearch).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    console.log(msg);
                    $scope.otpremaSearch.outputs = msg;
                    $('.searchPanelHead').find('i').switchClass( "fa-cog fa-spin", "fa-search", 0, "easeInOutQuad" );
                } else {
                    //not loged in
                    $scope.$parent.logoutUser();
                }
            }).error(function(error){
                console.log(error);
            });
        };

        $scope.selectOdabranuOtpremu = function(){
            $('.searchPanelHead').find('i').switchClass( "fa-search", "fa-cog fa-spin", 0, "easeInOutQuad" );
            otpremaMerkantilaPregledFactory.selectOdabranuOtpremu($scope.otpremaSearch).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    if(msg.length !== 0){
                        console.log(msg);
                        $scope.last_measurement = msg[0];
                        $scope.hideInputItems($scope.last_measurement.goods_type);
                        $scope.$parent.last_measurement = msg[0];
                        $scope.$parent.hideInputItems(msg[0].goods_type);

                    }
                    $('.searchPanelHead').find('i').switchClass( "fa-cog fa-spin", "fa-search", 0, "easeInOutQuad" );
                } else {
                    //not loged in
                    $scope.$parent.logoutUser();
                }
            }).error(function(error){
                console.log(error);
            });
        }
    };

    otpremaMerkantilaController.$inject = ['$scope', '$filter', 'otpremaMerkantilaPregledFactory', 'mainService', 'clientsFactory', 'errorService'];
    angular.module('_raiffisenApp').controller('otpremaMerkantilaController', otpremaMerkantilaController);

    /************************************************* /IZLAZ MERKANTILA CONTROLLER *************************************************************************************************************/



}());