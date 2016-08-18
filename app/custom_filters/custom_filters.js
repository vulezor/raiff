(function(){

    angular.module('_raiffisenApp').filter('capitalize', function() {
        return function(input) {
            if(typeof input !=='undefined'){
                return input.replace(/(\S)(\S*)/g, function($0,$1,$2){
                    return $1.toUpperCase()+$2.toLowerCase();
                });
            }
        }
    });

     var serbian_replace = function(){
        return function(input) {
            var arr = ['Š','Đ','Ć','Č','Ž','š','đ','č','ć','ž'];
            var riplace_arr = ['S','Dj','C','C','Z','s','dj','c','c','z',];
            for(var i=0;i<arr.length;i++){
                var forReplace = new RegExp(arr[i], 'g');
                input = input.replace(forReplace, riplace_arr[i]);
            }
            return input;
        }
    };
    angular.module('_raiffisenApp').filter('serbian_replace', serbian_replace);

}());