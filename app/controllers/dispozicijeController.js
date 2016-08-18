(function(){
    var dispozicijeController = function($scope, $filter ,clientsFactory, prijemRepromaterijalPregledFactory, dispozicijeFactory, errorService, errorService_second, usersFactory){
        $scope.insert_data = {};
        $scope.insert_data.reservation = [];
        $scope.insert_data.emails = [];
        $scope.wearehouses =[];
        $scope.clients =[];
        $scope.show_clients = false;
        $scope.show_dispozicija = false;
        $scope.goods = [];
        $scope.users = [];
        $scope.insert_data.users_id = [];
        $scope.active_wearehouse_name = '';
        $scope.create_reservation = {};
        $scope.create_reservation.goods = [];
        $scope.reservation_created = [];
        $scope.activeMenu = 'repromaterijal';
        $scope.emails = [];
        $scope.email_list = '';

        $scope.show_storniranje = false;          //visibility stornog kontejnera
        $scope.storna_dispozicija = "";
        $scope.napomena = "";

        //za izmenu i print
        $scope.dispozicija_izmena = false; //visibility ng-show
        $scope.aktivna_dispozicija = [];

        $scope.$watch("create_reservation", function( newVal, oldVal){
           console.log(newVal);
        }, true);


        $.datepicker.setDefaults( $.datepicker.regional[ "sr-SR" ] );
        $('input[name="datum_utovara"]').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd.mm.yy',
            onClose:function(){
                var name = $(this).attr('name');
               /* $scope.$apply(function(){*/
                    $scope.create_reservation[name] = $(this).val();
               // });

            }
        });

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

        usersFactory.getUsers().success(function(msg){
                $scope.users = msg;
        }).error(function(error){
            console.log(error)
        });

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
        };

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------
        $scope.showDisposition = function(){
            $scope.show_dispozicija = !$scope.show_dispozicija;
        };

        $('.black-background').height();
        var height = window.screen.availHeight - 300;
        $('.goods-wrapper').height(height);
        var height_half = $('.goods-wrapper').height() / 2;
        $('.goods-wrapper').css({'top':'50%', 'margin-top':'-'+height_half+'px'});
        $('.goods_body').height(height - 53);
        $( ".dispozicija_container" ).draggable({ handle: ".dispozicija_panel_header" });
        $scope.createDisposition = function(){
            if(!$scope.insert_data.hasOwnProperty('firm_name') ||  $scope.insert_data.firm_name===0 ||  $scope.insert_data.firm_name===null || typeof $scope.insert_data.firm_name === 'undefined'){
                errorService.error_msg($('input[name="firm_name"]'), "Morate prvo selektovati firmu!"); alert('Morate prvo selektovati firmu!');return false;
            }
            if(!$scope.insert_data.hasOwnProperty('wearehouse_id') ||  $scope.insert_data.wearehouse_id===0 ||  $scope.insert_data.wearehouse_id===null || typeof $scope.insert_data.wearehouse_id === 'undefined'){
                errorService.error_msg($('select[name="wearehouse"]'), "Morate izabrati magacin iz kojeg će ići otprema robe!"); alert('Morate izabrati magacin iz kojeg će ići otprema robe!'); return false;
            }
            $obj = {
                'client_id':$scope.insert_data.client_id,
                'wearehouse_id':$scope.insert_data.wearehouse_id
            };
           /* $scope.create_reservation.client_id = $scope.insert_data.client_id;
            $scope.create_reservation.wearehouse_id = $scope.insert_data.wearehouse_id;*/
                 $('.get_goods').find('i').switchClass( "fa-truck", "fa-cog fa-spin", 0, "easeInOutQuad" );
                dispozicijeFactory.getAllGoods($obj).success(function(msg){
                    $('.get_goods').find('i').switchClass( "fa-cog fa-spin", "fa-truck", 0, "easeInOutQuad" );
                    if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                        $scope.goods = msg;
                        $scope.showDisposition();
                        console.log($scope.goods);
                    } else {
                        console.log('not logedin');
                        //logout
                        window.location.href = mainService.domainURL();
                    }
                }).error(function(error){
                    console.log(error)
                });
        };

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.onWearehouseChange = function(){
            single_object = $filter('filter')($scope.wearehouses, function (d) {return d.wearehouse_id === $scope.insert_data.wearehouse_id;})[0];
            $scope.active_wearehouse_name = single_object.wearehouse_name;
        };

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.setActiveMenu = function(key){
           /* console.log(key);
            console.log($scope.create_reservation.goods.length);*/
            if((key === 'repromaterijal' && $scope.activeMenu === 'merkantila') || ( key === 'rezervisano' && $scope.activeMenu === 'merkantila' )){
                if($scope.create_reservation.goods.length!==0){
                    r = confirm("Nije moguće dodeliti repromaterijal u stavci u kojoj ste dodelili merkantilu!\n\nMerkantilna roba koju ste dodelili u ovom kreiranju će se izbrisati. Da li stvarno želite to da uradite!");
                } else{
                    $scope.activeMenu = key;
                }
                if(r===true){
                    $scope.activeMenu = key;
                    $scope.create_reservation.goods = [];
                }else{
                    setActiveMenu($scope.activeMenu);
                }
            } else if(( key === 'merkantila' && $scope.activeMenu === 'repromaterijal' ) || ( key === 'merkantila' && $scope.activeMenu === 'rezervisano' )){
                var r;
                if($scope.create_reservation.goods.length!==0){
                     r = confirm("Nije moguće dodeliti merkantilu u stavci u kojoj ste dodelili repromaterijal i rezervisanu robu!\n\nRepromaterijal i rezervisanu robu koju ste dodelili u ovom kreiranju će se izbrisati. Da li stvarno želite to da uradite!");
                } else{
                    $scope.activeMenu = key;
                }
                if(r===true){
                    $scope.activeMenu = key;
                    $scope.create_reservation.goods = [];
                }else{
                    setActiveMenu($scope.activeMenu);
                }
            } else {
                $scope.activeMenu = key;
            }

        };

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.orderGoods = function(element){
            console.log(element);
            var kolicina =  element.kolicina || 0;
            var arr =
            {
                goods_id:element.goods_id,
                sort_of_goods_id: element.sort_of_goods_id,
                type_of_goods_id: element.type_of_goods_id,
                lot: '',
                quantity: kolicina,
                goods_name:element.goods_name,
                measurement_unit: element.measurement_unit
            };
            if(element.hasOwnProperty('reservation_id')){
                arr.sa_rezervacije = "y";
                arr.reservation_id = element.reservation_id
            }
            obj = _.filter($scope.create_reservation.goods, function(item) { return item.goods_id == element.goods_id });
           // console.log(obj);
          /*  if( obj.length == 0 ){*/
                $scope.create_reservation.goods.push(arr);
         /*   } else {
                console.log('postoji');
            };*/
        };

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.deleteFromOrder = function(obj){
            var myarr = $scope.create_reservation.goods, arr;
            arr = _.without(myarr, _.findWhere(myarr, {goods_id: obj.goods_id}));
            $scope.create_reservation.goods = arr;
            console.log(arr);
            //arr = _.without(arr, _.findWhere(arr, {id: 3}));
        };




        //----------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.setDisposition = function(){
            if(!$scope.create_reservation.hasOwnProperty('vozac') ||  $scope.create_reservation.vozac===0 ||  $scope.create_reservation.vozac===null || typeof $scope.create_reservation.vozac === 'undefined'){
                errorService.error_msg($('input[name="vozac"]'), "Morate upisati ime vozaca!"); return false;
            }
            if(!$scope.create_reservation.hasOwnProperty('reg_table') ||  $scope.create_reservation.reg_table===0 ||  $scope.create_reservation.reg_table===null || typeof $scope.create_reservation.reg_table === 'undefined'){
                errorService.error_msg($('input[name="reg_table"]'), "Morate upisati registraviju vozila!");  return false;
            }
            if(!$scope.create_reservation.hasOwnProperty('datum_utovara') ||  $scope.create_reservation.datum_utovara===0 ||  $scope.create_reservation.datum_utovara===null || typeof $scope.create_reservation.datum_utovara === 'undefined'){
                errorService.error_msg($('input[name="datum_utovara"]'), "Morate označiti datum utovara!"); return false;
            }
            if(!$scope.create_reservation.hasOwnProperty('mesta_istovara') ||  $scope.create_reservation.mesta_istovara===0 ||  $scope.create_reservation.mesta_istovara===null || typeof $scope.create_reservation.mesta_istovara === 'undefined'){
                errorService.error_msg($('input[name="mesta_istovara"]'), "Morate upisati kranja mesta/mesto istovara!");  return false;
            }

            if($scope.create_reservation.goods.length !== 0 ){
                if($scope.checkOrderSet() !== false){
                    console.log($scope.create_reservation)
                    $scope.insert_data.reservation.push($scope.create_reservation);
                    console.log( $scope.insert_data);
                    $scope.create_reservation = {};
                    $scope.create_reservation.goods = [];
                }
            } else {
                errorService_second.error_msg($('.dispozicija_priprema_body'), "Morate dodati makar jedan proizvod da bi ste potvrdili ulaz robe!"); return false;
            }

        };

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.checkOrderSet = function(){
            var status = true;
            $.each($scope.create_reservation.goods, function(i, item){
                if(item.quantity <= 0){
                    errorService_second.error_msg($('.dispozicija_priprema_body'), "proizvodu "+item.goods_name+" niste dodelili kolicinu za rezervaciju!"); status=false; return false;
                }
            });
            return status;
        };

        $scope.stateChanged = function (email) {
            if($scope.emails[email]){ //If it is checked
                $scope.insert_data.emails.push({email:email});
                var str = '';
               for(m in $scope.insert_data.emails){
                    str += $scope.insert_data.emails[m].email+', ';
               }
                $scope.email_list = str.slice(0, -2);
                console.log($scope.email_list)
            } else{
                var myarr = $scope.insert_data.emails, arr;
                arr = _.without(myarr, _.findWhere(myarr, {email: email}));
                $scope.insert_data.emails = arr;
                var str = '';
                for(m in $scope.insert_data.emails){
                     str += $scope.insert_data.emails[m].email+', ';
                }
                $scope.email_list = str.slice(0, -2);
                console.log($scope.email_list);
                //arr = _.without(arr, _.findWhere(arr, {id: 3}));

            }
        };

        $('.lista_mailova').hover(function(){
            $('.lista_mailova').css({'border':'1px solid #ccc'}).stop().animate({'height':'150px'}, 300, 'swing');
        }).mouseleave(function(){
            $('.lista_mailova').stop().animate({'height':'0px'}, 300, 'swing', function(){
                $('.lista_mailova').css({'border':'0px solid transparent'})
            });
        });
        $('input[name="email_list"]').click(function(){
            $('.lista_mailova').css({'border':'1px solid #ccc'}).stop().animate({'height':'150px'}, 300, 'swing');
        }).mouseleave(function(){
            $('.lista_mailova').stop().animate({'height':'0px'}, 300, 'swing', function(){
                $('.lista_mailova').css({'border':'0px solid transparent'})
            });
        });



        $scope.saveDisposition = function(){
            if(!$scope.insert_data.hasOwnProperty('firm_name') ||  $scope.insert_data.firm_name===0 ||  $scope.insert_data.firm_name===null || typeof $scope.insert_data.firm_name === 'undefined'){
                errorService.error_msg($('input[name="firm_name"]'), "Morate odabrati kupca nego što potvrdite dispoziciju!"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('wearehouse_id') ||  $scope.insert_data.wearehouse_id===0 ||  $scope.insert_data.wearehouse_id===null || typeof $scope.insert_data.wearehouse_id === 'undefined'){
                errorService.error_msg($('select[name="wearehouse"]'), "Morate selektovati magacin potvrdite dispoziciju !"); return false;
            }
            if(!$scope.insert_data.hasOwnProperty('reservation') ||  $scope.insert_data.reservation.length===0  ||  $scope.insert_data.reservation===0 ||  $scope.insert_data.reservation===null || typeof $scope.insert_data.reservation === 'undefined'){
                errorService.error_msg($('button.get_goods'), "Nemate kreiranu dispoziciju !"); return false;
            }
          console.log($scope.insert_data);
            $('.ajax_load_disposition_visibility').css({'visibility':'visible'});
            dispozicijeFactory.saveDisposition($scope.insert_data).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    console.log(msg);
                    $scope.insert_data = {};
                    $scope.insert_data.reservation = [];
                    $scope.insert_data.emails = [];
                    $('.ajax_load_disposition_visibility').css({'visibility':'hidden'});
                    $scope.$broadcast('update_search_table_dispozicija');
                } else {
                    console.log('not logedin');
                    window.location.href = mainService.domainURL();//logout
                }
            }).error(function(error){
                console.log(error);
            })
        };

        $scope.deleteInsertData = function(obj){
            var myarr = $scope.insert_data.reservation, arr;
            arr = _.without(myarr, _.findWhere(myarr, {vozac: obj.vozac}));
            $scope.insert_data.reservation = arr;
            console.log(arr);
            //arr = _.without(arr, _.findWhere(arr, {id: 3}));
        };

        $('.storni_container').draggable({handle:'.storni_panel_header'});
        $scope.stornirajDispoziciju = function(){
            var obj = {
                dispozicija_id: $scope.storna_dispozicija.dispozicija_id,
                napomena: $scope.napomena
            };
            if(!obj.hasOwnProperty('napomena') ||  obj.napomena===0 ||  obj.napomena===null || typeof obj.napomena === 'undefined'){
                errorService_second.error_msg($('textarea[name="napomena"]'), "Morate upisati razlog storniranja!"); return false;
            }
            $('.ajax_storno').css({'visibility':'visible'})
            dispozicijeFactory.storniraj_dokument(obj).success(function(msg){
                $('.ajax_storno').css({'visibility':'hidden'})
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $scope.show_storniranje = false;
                    $scope.napomena = '';
                    $scope.$broadcast('update_search_table_dispozicija');
                } else {
                    console.log('not logedin');
                    //logout
                    window.location.href = mainService.domainURL();
                }
            }).error(function(){
                console.log(error);
            });
        };

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------
        $scope.changeQuantity = function($event, kolicina, lot, stavka_id){
            var obj = {
                'lot':lot,
                'kolicina':kolicina,
                'stavka_id':stavka_id,
                'session_id':$scope.session_id
            };
            $($event.currentTarget).html('Izmeni <i class="fa fa-cog fa-spin"></i>');
            dispozicijeFactory.changeKolicinu(obj).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $($event.currentTarget).html('Izmeni')
                } else {
                    //not loged in
                    $scope.logoutUser();
                }
            }).error(function(error){
                console.log(error);
            })
        };

        $scope.print = function(){
            var content = $('.dispozicija_b').html();
            $('.print_area').html(content);
            window.print();
        };

    };
    dispozicijeController.$inject = ['$scope','$filter', 'clientsFactory', 'prijemRepromaterijalPregledFactory', 'dispozicijeFactory', 'errorService', 'errorService_second', 'usersFactory'];
    angular.module('_raiffisenApp').controller('dispozicijeController', dispozicijeController);


    //***************************************************************************************************************************************************************

    var pregledDispozicijaTable = function( DTOptionsBuilder, DTColumnBuilder, usersFactory, $scope, $compile, mainService, dispozicijeFactory, $timeout) {
        var vm = this;
        $scope.show_prijem_repromaterijal=true;
        // vm.stornirajRezervaciju =stornirajRezervaciju;
        vm.predStorniranje = predStorniranje;
        vm.rowNum = 0;
        vm.reloadData = reloadData;
        vm.resetData = resetData;
        vm.dtInstance = {};
        vm.dispositionResult = {};
        vm.result=null;
        vm.good_type=null;
        vm.printDispozicija = printDispozicija;
        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.$on("update_search_table_dispozicija", function(event) {
            reloadData();
        });

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.$on("reset_search_table_prijem", function(event) {
            //console.log($scope.$parent.link);
            resetData();
        });

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        vm.dtOptions = DTOptionsBuilder
            .fromSource(mainService.domainURL()+'dispozicije_api/get_dispozicija')
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

                    {type: 'text', bRegex: true, bSmart: true},
                    {type: 'select', bRegex: false, values: ['y', 'n']},
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
            DTColumnBuilder.newColumn('dispozicija_id').withTitle('ID').withOption('width', '60px')/*.renderWith(actionsEditUser)*/ ,
            DTColumnBuilder.newColumn('datum_kreiranja').withTitle('Datum kreiranja').withOption('width', '80px'),
            DTColumnBuilder.newColumn('firm_name').withTitle('Firma/Gazdinstvo'),
            DTColumnBuilder.newColumn('wearehouse_name').withTitle('Iz Magacina'),
            DTColumnBuilder.newColumn('user_name').withTitle('Dokument Kreirao'),
            DTColumnBuilder.newColumn('realizovana').withTitle('Realizacija'),
            DTColumnBuilder.newColumn(null).withTitle('Storniranje').notSortable().withOption('width','80px').renderWith(actionsHtml).withClass('text-center')
        ];

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        function reloadData() {
            console.log(vm.result);
            var resetPaging = false;
            vm.dtInstance.changeData(mainService.domainURL()+'dispozicije_api/get_dispozicija');
        }

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        function resetData() {
            var resetPaging = false;
            vm.dtInstance.changeData(mainService.domainURL()+'pregled_prijema_repromaterijal_admin_api/empty_load');
        }

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------

        function predStorniranje(id){
            console.log(id);
            $scope.$parent.show_storniranje = true;
            $scope.$parent.storna_dispozicija = id;
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

        function printDispozicija(id){
            //console.log(id);return false;
            /*$scope.$parent.aktivna_dispozicija = id;*/
            dispozicijeFactory.getDisposition({'dispozicija_id':id.dispozicija_id}).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $scope.$parent.aktivna_dispozicija = msg;
                    console.log(msg);
                    $scope.$parent.dispozicija_izmena = true;
                } else {
                    console.log('not logedin');
                    window.location.href = mainService.domainURL();//logout
                }
            }).error(function(error){
                console.log(error);
            });
            /*$scope.$parent.hideInputItems(id.goods_type);*/
           /* window.setTimeout(function(){$scope.print();}, '200');*/
        }

        function actionsHtml (data, type, full, meta) {
            var t ='';
            vm.dispositionResult[data.dispozicija_id] = data;
            if($scope.$parent.session_info === 'Administrator' && $scope.$parent.session_info === 'Logistika' || $scope.$parent.session_info === undefined ){
                t+='';
                if(data.realizovana !== "y"){
                   t+='<i class="fa fa fa-edit btn btn-primary btn-xs" style="margin-top:0px" title="Izmeni dispoziciju" data-ng-click="dispozicijaShowCase.printDispozicija(dispozicijaShowCase.dispositionResult[' + data.dispozicija_id + '])"></i><i class="fa fa fa-exclamation-triangle btn btn-danger btn-xs" style="margin-top:0px;margin-left:5px" title="Storniraj dispoziciju ' + data.dispozicija_id + '" data-ng-click="dispozicijaShowCase.predStorniranje(dispozicijaShowCase.dispositionResult[' + data.dispozicija_id + '])"></i>';
                }else {
                    t+='<i class="fa fa fa-file btn btn-primary btn-xs" style="margin-top:0px" title="pogledaj dispoziciju" data-ng-click="dispozicijaShowCase.printDispozicija(dispozicijaShowCase.dispositionResult[' + data.dispozicija_id + '])"></i>';
                }
            } else {
                t='';
            }
            return t;
        }


    };
    pregledDispozicijaTable.$inject = ['DTOptionsBuilder', 'DTColumnBuilder', 'usersFactory', '$scope', '$compile', 'mainService', 'dispozicijeFactory', '$timeout'];
    angular.module('_raiffisenApp').controller('pregledDispozicijaTable', pregledDispozicijaTable);



}());