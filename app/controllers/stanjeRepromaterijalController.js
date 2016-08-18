(function(){
    var stanjeRepromaterijalController = function($scope, stanjeRepromaterijalFactory, infoboxService){
        $scope.repromaterijal = [
            {id:6, naziv:'Hemija'},
            {id:7, naziv:'Seme'},
            {id:9, naziv:'Djubrivo'},
            {id:15, naziv:'Razna Roba'}
        ];
        $scope.send_data = [];
        $scope.wearehouses=[];
        $scope.results = [];
        $scope.napomena = '';

        //--------------------------------------------------------------------------------------------------------
        stanjeRepromaterijalFactory.get_wearehouses().success(function(msg){
            if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                $scope.wearehouses=msg;
            } else {
                console.log('not logedin');
                //logout
                window.location.href = mainService.domainURL();
            }
        }).error(function(error){
            console.log(error);
        });

        //--------------------------------------------------------------------------------------------------------

        $scope.getResults = function(){
            stanjeRepromaterijalFactory.get_results($scope.send_data).success(function(msg){
                if(msg.logedIn !== 0 && msg.hasOwnProperty('logedIn')===false){
                    $scope.results = msg.svi_rezultati;
                    $scope.napomena = msg.napomena;
                } else {
                    console.log('not logedin');
                    //logout
                    window.location.href = mainService.domainURL();
                }
            }).error(function(error){
                console.log(error);
            });
        };

        //--------------------------------------------------------------------------------------------------------

        $scope.print = function(){
            var content = $('.print_rezervacija').html();
            $('.print_area').html(content);
            window.print();
        };

        //init
        $scope.getResults();
    };
    stanjeRepromaterijalController.$inject = ['$scope', 'stanjeRepromaterijalFactory', 'infoboxService'];
    angular.module('_raiffisenApp').controller('stanjeRepromaterijalController', stanjeRepromaterijalController);
}());