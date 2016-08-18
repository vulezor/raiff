(function(){
    var smartInput = function () {
        return {
            restrict: 'E',
            compile: function(element, attrs)
            {
                var type = attrs.type || 'text';
                var sectionClass = attrs.sectionClass ||'col col-sm-6 fixsection';
                var labelClass = attrs.labelClass || 'input';
                var icon = attrs.icon || 'icon-prepend fa fa-user';
                var inputClass = attrs.inputClass || 'input-sm';
                var placehold =  attrs.placehold || '';
                var maxl = attrs.maxl || '';
                var minl = attrs.minl || '';
                var ngclass = 'data-ng-class="'+attrs.ngclass+'"' || '';
                var directive = attrs.directive || '';
                var readonly = attrs.readonly || '';
                var action = attrs.action || '';
                var req = attrs.hasOwnProperty('required') ? "req='required'" : "";
                var htmlText ='<section class="'+ sectionClass +'">' +
                    '<label class="'+ labelClass + '" '+ngclass+'>' +
                    '<i class="' + icon + '" data-boxinfo="' + placehold + '"></i>' +
                    '<input type="text" class="'+inputClass+'"  name="'+attrs.name+'" placeholder="'+placehold+'" '+readonly+' '+action+' data-ng-model="'+attrs.model+'"  minlength="'+minl+'" maxlength="'+maxl+'" ' + directive + ' ' + req + '/>' +
                    '</label>' +
                    '</section>';
                element.replaceWith(htmlText);
            }
        }
    };
    angular.module('_raiffisenApp').directive('smartInput', smartInput);

    var smartSelect = function () {
        return {
            restrict: 'E',
            compile: function(element, attrs)
            {
                var sectionClass = attrs.sectionClass ||'col col-sm-6 fixsection';
                var labelClass = attrs.labelClass || 'input';
                var icon = attrs.icon || 'icon-prepend fa fa-user';
                var selectClass = attrs.inputClass || 'input-sm';
                var placehold =  attrs.placehold || '';
                var directive = attrs.directive || '';
                var action = attrs.action || '';
                var req = attrs.hasOwnProperty('required') ? "req='required'" : "";
                var htmlText ='<section class="'+sectionClass+'"> ' +
                        '<label class="'+labelClass+'"> ' +
                            '<select name="'+attrs.name+'" id="'+attrs.name+'" class="' + selectClass + '"  data-ng-model="' + attrs.model + '" ng-options="' + attrs.options + '" '+ action+' ' + req + '> ' +
                                '<option value="">' + placehold + '</option> ' +
                            '</select>' +
                            '<i class="'+icon+'" data-boxinfo="' + placehold + '"></i> ' +
                        '</label> ' +
                    '</section>';
                element.replaceWith(htmlText);
            }
        }
    };
    angular.module('_raiffisenApp').directive('smartSelect', smartSelect);
}());