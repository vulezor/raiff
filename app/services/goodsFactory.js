(function(){
    var goodsFactory = function($http){
        var factory = {};

        factory.getGoods = function(){
            return $http.get('goods_api/get_goods');
        };

        factory.getGoods = function(id){
            return $http.get('goods_api/get_goods/' + id);
        };

        factory.insertGoods = function(good){
            return $http.post('goods_api/insert_goods', good);
        };

        factory.updateGoods = function(good){
            return $http.put('goods_api/update_goods/' + good.goods_id, good);
        };

        factory.deleteGoods = function (id) {
            return $http.delete('goods_api/delete_goods/' + id);
        };

        factory.getMeasureUnit = function(){
            return $http.get('goods_api/get_measurement_unit');
        };

        factory.getGoodsType = function(sort){
            return $http.get('goods_api/get_goods_type/' + sort);
        };

        factory.getGoodsClass = function(){
            return $http.get('goods_api/get_goods_class');
        };

        factory.insertMeasureUnit = function(new_measure_unit){
            return $http.post('goods_api/insert_measurement_unit', new_measure_unit);
        };

        return factory;
    };
    goodsFactory.$inject = ['$http'];
    angular.module('_raiffisenApp')
        .factory('goodsFactory', goodsFactory);
//-------------------------------------------------------------------------------

}());