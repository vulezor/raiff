(function(){
    var fileModel = function ($parse) {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                var model = $parse(attrs.fileModel);
                var modelSetter = model.assign;

                element.bind('change', function(){
                    scope.$apply(function(){
                       console.log(element[0].files[0].name);
                        var parts = element[0].files[0].name.split('.');
                        parts = parts[parts.length - 1];
                        if(parts === 'xlsx' || parts === 'xls'){
                            modelSetter(scope, element[0].files[0]);
                            scope.uploadFile();

                        } else {
                           $('#myfile').val('');
                            alert('Dozvoljeni tip fajla xls ili xlsx');
                        }

                    });
                });
            }
        };
    };
    fileModel.$inject = ['$parse'];
    angular.module('_raiffisenApp').directive('fileModel', fileModel);
}());