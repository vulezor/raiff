(function(){
    var dashboardController = function($scope, $timeout, dashboardFactory){
        $scope.locations = [
            {
                bruto_polje: "otkljucano",
                latitude: "44.816343",
                longitude: "20.419153",
                wearehouse_address: "Булевар Зорана Ђинђића 67, Београд",
                wearehouse_id: 0,
                icon: "public/img/rlogo.png",
                wearehouse_name: "Raiffeisen Agro Doo"
         }
            ];

        //submenu repromaterijal var
        $scope.mg_info = '';
        $scope.svi_rezultati =[];
        $scope.sub_key = 'Hemija';


        $scope.odabir_datuma = '';

        $scope.daily_outputs = [];

        $scope.last_measurement={};
        $scope.ifMereno = false;

        $scope.enableDays = [];
       // $scope.locations = [];
        dashboardFactory.getWearehouseInfo().success(function(msg){
            if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false) {
                for(m in msg){
                    msg[m].icon = 'public/img/raiffsilo.png';
                    $scope.locations.push(msg[m]);
                }
                $scope.mapRender($scope.locations);
                $scope.stanjeMagacinaInfo(1);
            } else {
                //not loged in
                window.location.href = mainService.domainURL();
            }
        }).error(function(error){
            console.log(error);
        });

        $scope.mapRender = function(locations){
            console.log(locations);
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8,
                center: new google.maps.LatLng(45.192684, 19.85186),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            var infowindow = new google.maps.InfoWindow();

            var marker, i;

            for (i = 0; i < locations.length; i++) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i].latitude, locations[i].longitude),
                    icon: locations[i].icon,
                    map: map
                });

                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                         var magacin = locations[i].wearehouse_id === 0 ? 'Sedište' : 'Magacin';
                         infowindow.setContent(magacin+': '+locations[i].wearehouse_name+', '+locations[i].wearehouse_address);
                         infowindow.open(map, marker);
                        $scope.stanjeMagacinaInfo(locations[i].wearehouse_id);

                    }
                })(marker, i));
            }

            $scope.stanjeMagacinaInfo = function(wearehouse_id){
            console.log(wearehouse_id+', proba')
                dashboardFactory.stanjeMagacinaInfo({'wearehouse':wearehouse_id}).success(function(msg){
                    $scope.mg_info = 'Magacin: '+msg.magacin;
                    $scope.svi_rezultati = msg.svi_rezultati;
                    $scope.getGoodType($scope.sub_key)
                }).error(function(error){
                    console.log(error)
                });
            }
        };

        $scope.getGoodType = function(key){
         // console.log($scope.svi_rezultati[key]);
            $scope.rep_good_type = $scope.svi_rezultati[key];
            $scope.sub_key = key;
            console.log($scope.rep_good_type);
        };

        /*$('input[name="odabir_datuma"]').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd.mm.yy',
            onClose:function(){
                $scope.odabir_datuma = $(this).val();
                $scope.getDayOutputs();
            }
        });*/
        //$scope.enableDays = ["7-8-2013","1-12-2015","4-12-2015","9-12-2015","15-12-2015"];//in mysql date format like DATE_FORMAT(DATE(input_records.input_date),'%e-%c-%Y')*/

        $scope.enableAllTheseDays = function(date) {
            var sdate = $.datepicker.formatDate( 'd-m-yy', date)

            if($.inArray(sdate, $scope.enableDays) != -1) {
                return [true,"","Zabeležena otprema"];
            }
            return [false,"","Nema beležene otpreme"];
        };

        $scope.enableDays = function(weareouse_id){
            var wearehouse_id = weareouse_id || null;
            $('input[name="odabir_datuma"]').datepicker( "destroy" );
            dashboardFactory.enableDays({'wearehouse_id':wearehouse_id}).success(function(msg){
                    $scope.enableDays = [];
                    for(i=0;i<msg.output_days.length;i++){
                        $scope.enableDays.push(msg.output_days[i].datum);
                    }
                    $.datepicker.setDefaults( $.datepicker.regional[ "sr-SR" ] );
                    $('input[name="odabir_datuma"]').datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'dd.mm.yy',
                        beforeShowDay: $scope.enableAllTheseDays,
                        onClose:function(){
                            $scope.odabir_datuma = $(this).val();
                            $scope.getDayOutputs();
                        }
                    });
                    var pocetni_datum = msg.last_output_day[0].last_day;
                    $('input[name="odabir_datuma"]').val(pocetni_datum);
                    $scope.odabir_datuma = pocetni_datum;
                    $scope.getDayOutputs();

            }).error(function(){
                console.log('error');
            });
        };

        $scope.getDayOutputs = function(){
            dashboardFactory.getDayOutputs({'date':$scope.odabir_datuma}).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false) {
                    $scope.daily_outputs = msg;
                    console.log(msg);
                } else {
                    window.location.href = mainService.domainURL();//logout
                }
            }).error(function(){
                console.log(error);
            });
        };

        $scope.getOtpremnica = function(output_id){
            dashboardFactory.getOtpremnica({'output_id':output_id}).success(function(msg){
                    $scope.last_measurement = msg;
                if(msg.outputs[0].bruto === "0.000"){
                    $scope.ifMereno = true;
                    $timeout(function(){$scope.print()}, 200);
                } else {
                    $scope.ifMereno = false;
                    $timeout(function(){$scope.print()}, 200);
                }
            }).error(function(){
                console.log(error);
            });
        };

        $scope.print = function(){
            var content = $('.print_otprema').html();
            $('.print_area').html(content);
            window.print();
        };

        $scope.today = function(){
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!
            var yyyy = today.getFullYear();

            if(dd<10) {
                dd='0'+dd
            }

            if(mm<10) {
                mm='0'+mm
            }

            return dd+'.'+mm+'.'+yyyy;
        };
        $scope.enableDays();

        var pocetni_datum = $scope.today();
        $('input[name="odabir_datuma"]').val(pocetni_datum);
        $scope.odabir_datuma = pocetni_datum;
        $scope.getDayOutputs();
    };
    dashboardController.$inject = ['$scope', '$timeout', 'dashboardFactory'];
    angular.module('_raiffisenApp').controller('dashboardController', dashboardController);
}());