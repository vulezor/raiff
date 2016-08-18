(function(){
    var editGoodsController = function($scope, $filter, $routeParams, goodsFactory, errorService){
        $scope.goodsdata = {};
        $scope.goodsVisible = true;
        $scope.new_measure_unit = {};
        //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        goodsFactory.getGoods($routeParams.goods_id).success(function(msg){
            $scope.goodsdata  = msg[0];
            $scope.showGoodsSort();
            $scope.goodsdata.sort_of_type_id = msg[0].type_of_goods_id;
            console.log($scope.goodsdata.type_of_goods_id);
        }).error(function(error){
            console.log(error);
        });

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

        $scope.doVisible = function(){
            $scope.goodsVisible = $scope.goodsVisible===true ? false : true; //show, hide of new place panel on click
        };

        //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $scope.addNewMeasureUnit = function(){

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

        $scope.updateGoods = function(){

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
            goodsFactory.updateGoods($scope.goodsdata).success(function(msg){
                $('.ajax_load_visibility').css('visibility','hidden');
                console.log(msg);
                if(msg.success===0){
                 alert('Informacije su izmenjene');
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
        }


    };
    editGoodsController.$inject = ['$scope', '$filter', '$routeParams', 'goodsFactory', 'errorService'];
    angular.module('_raiffisenApp').controller('editGoodsController', editGoodsController)
}());