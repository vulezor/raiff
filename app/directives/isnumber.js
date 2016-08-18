(function(){
    angular.module('_raiffisenApp')
    .directive('isNumber', function() {
        return {
            require: '?ngModel',
            link: function(scope, element, attrs, ngModelCtrl) {
                if(!ngModelCtrl) {
                    return;
                }

                ngModelCtrl.$parsers.push(function(val) {
                    if (angular.isUndefined(val)) {
                        var val = '';
                    }
                    var clean = val.replace(/[^0-9\.]/g, '');
                    var decimalCheck = clean.split('.');

                    if(!angular.isUndefined(decimalCheck[1])) {
                        decimalCheck[1] = decimalCheck[1].slice(0, 6);
                        console.log(decimalCheck[1]);
                        clean =decimalCheck[0] + '.' + decimalCheck[1];
                    }

                    if (val !== clean) {
                        var offset = $(element).offset();
                        var loffset = offset.left;
                        var toffset = offset.top;
                        if($('body').find('.alert_msg').length<1){
                            $('body').append('<div style="left:' + loffset + 'px;top:' + toffset + 'px" class="alert_msg">U ovom polju su samo brojevi dozvoljeni</div>');

                            $('body').click(function(){
                                $('.alert_msg').remove();
                                $( 'body').unbind('click');
                            });
                        }

                        ngModelCtrl.$setViewValue(clean);
                        ngModelCtrl.$render();
                    }
                    return clean;
                });

                element.bind('keypress', function(event) {
                    if(event.keyCode === 32) {
                        event.preventDefault();
                    }
                });
            }
        };
    });
}());