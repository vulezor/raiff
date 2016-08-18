(function() {
    var winresize = function ($window) {
        return function (scope, element) {
            var w = angular.element($window);

            scope.getWindowDimension = function () {
                return {
                    'h': w.height(),
                    'w': w.width()
                };
            };

            scope.$watch(scope.getWindowDimension, function (newValue, oldValue) {

                scope.windowHeight = newValue.h;
                scope.windowWidth = newValue.w;
               // console.log(scope.windowWidth+', '+scope.windowHeight);
            }, true);

            w.bind('resize', function () {
                scope.$apply();
            });
        }
    };
    winresize.$inject = ['$window'];
    angular.module('_raiffisenApp').directive('winresize', winresize);
}());