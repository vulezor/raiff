(function(){
	angular.module('_raiffisenApp').directive('autocompleteClients', function(kupacRepromaterijalFactory, mainService){
		return{
			restrict: 'E',
			//replace:true,
			scope:{
				klijenti:'=',
				callback:'&'
			},
			controller:function($scope){
				$scope.hide_kupac =false;
				$scope.trazi_kupca = '';
				$scope.unhide = function(){
					$scope.hide_kupac = !$scope.hide_kupac;
				}

				$scope.hide = function(){
		            $scope.hide_kupac = !$scope.hide_kupac;
		            $scope.trazi_kupca = '';
		            window.setTimeout(function(){
		                $('input[name="filter_kupca"]').focus();
		            }, 200);
		        };
		        $scope.odabirKupca = function(index){
		        	console.log(index)
		        	$scope.odabrani_firm_name = $scope.klijenti[index].firm_name;
		        	$scope.callback()(index);
		        }
			},

			templateUrl: 'app/components/templates/autocompleteClients.html'
		}
	});	  
})();