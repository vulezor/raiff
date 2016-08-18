(function() {
    var formInput = function () {
        return {
            restrict: 'E',
            compile: function(element, attrs)
            {
                var type = attrs.type || 'text';
                var labelClass = attrs.labelClass || 'col-sm-5 control-label font-sm';
                var divClass = attrs.divClass || 'col-md-7';
                var inputClass = attrs.inputClass || 'form-control input-sm';
                var maxl = attrs.maxl || '';
                var minl = attrs.minl || '';
                var directive = attrs.directive || '';
                var required = attrs.hasOwnProperty('required') ? "required='required'" : "";
                var htmlText ='<section class="form-group">' +
                    '<label class="'+labelClass+'" for="' + attrs.formId + '">' + attrs.label + '</label>' +
                    '<div class="'+divClass+'">' +
                    '<input type="' + type + '" id="' + attrs.formId + '" name="' + attrs.formId + '" maxlength="'+maxl+'" minlength="'+minl+'" class="'+inputClass+'" ng-model="' + attrs.model + '" ' + directive +' '+ required + '>' +
                    '</div>' +
                    '</section>';
                element.replaceWith(htmlText);
            }
        }
    };
    angular.module('_raiffisenApp').directive('formInput', formInput);

    var formSelect = function () {
        return {
            restrict: 'E',
            compile: function(element, attrs)
            {
                var labelClass = attrs.labelClass || 'col-sm-5 control-label font-sm';
                var divClass = attrs.divClass || 'col-md-7';
                var inputClass = attrs.selectClass || 'form-control input-sm';
                var options = attrs.options || '';
                var directive = attrs.directive || '';
                var required = attrs.hasOwnProperty('required') ? "required='required'" : "";
                var htmlText ='<section class="form-group">' +
                    '<label class="'+labelClass+'" for="' + attrs.formId + '">' + attrs.label + '</label>' +
                    '<div class="'+divClass+'">' +
                    '<select id="' + attrs.formId + '" name="' + attrs.formId + '" class="'+inputClass+'" ng-model="' + attrs.model + '" ng-options="'+options+'"><option value="">----------Izaberi----------</option></select>' +
                    '</div>' +
                    '</section>';
                element.replaceWith(htmlText);
            }
        }
    };
    angular.module('_raiffisenApp').directive('formSelect', formSelect);


    var customValidation = function($filter){
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, element, attrs, ngModel) {
                element.bind('keypress', function () {
                    scope.$apply(setAnotherValue);
                });

                function setAnotherValue() {
                    ngModel.$parsers.push(function (value) {
                        value = $filter('serbian_replace')(value);
                        value = $filter('capitalize')(value);
                        ngModel.$setViewValue(value);
                        ngModel.$render();
                        return value;
                    });
                }
            }
        };
    };
    customValidation.$inject = ['$filter'];
    angular.module('_raiffisenApp').directive('customValidation', customValidation);

    var capitalizeInInput = function($filter){
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, element, attrs, ngModel) {
                element.bind('keypress', function () {
                    scope.$apply(setAnotherValue);
                });

                function setAnotherValue() {
                    ngModel.$parsers.push(function (value) {
                        value = $filter('uppercase')(value);
                        ngModel.$setViewValue(value);
                        ngModel.$render();
                        return value;
                    });
                }
            }
        };
    };
    capitalizeInInput.$inject = ['$filter'];
    angular.module('_raiffisenApp').directive('capitalizeInInput', capitalizeInInput);
}());

