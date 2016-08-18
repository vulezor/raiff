(function(){
    var errorService = function(){

        this.error_msg = function(elem, msg){
            var offset = elem.offset();
            var loffset = offset.left;
            var toffset = offset.top;
            if($('body').find('.alert_msg').length<1){
                $('body').append('<div style="top:'+(toffset+10)+'px;left:'+loffset+'px;opacity:0;z-index:1" class="alert_msg red"> <i class="fa fa-exclamation-triangle"></i> '+msg+'</div>');
                $('.alert_msg').animate({'top':toffset+'px','opacity':1}, 400, 'swing');
                elem.addClass('redBorder');
                elem.focus();
                $('body').click(function(){
                    elem.removeClass('redBorder');
                    $('.alert_msg').remove();
                    $( 'body').unbind('click');
                });
            }
        }
    };
  //  errorService.$inject = [];
    angular.module('_raiffisenApp').service('errorService', errorService);

    var errorService_second = function(){

        this.error_msg = function(elem, msg){
            var offset = elem.offset();
            var loffset = offset.left;
            var toffset = offset.top + elem.height() -28;
            if($('body').find('.alert_msg').length<1){
                $('body').append('<div style="top:'+(toffset+10)+'px;left:'+loffset+'px;opacity:0;z-index:1" class="alert_msg red"> <i class="fa fa-exclamation-triangle"></i> '+msg+'</div>');
                $('.alert_msg').animate({'top':toffset+'px','opacity':1}, 400, 'swing');
                elem.addClass('redBorder');
                elem.focus();
                $('body').click(function(){
                    elem.removeClass('redBorder');
                    $('.alert_msg').remove();
                    $( 'body').unbind('click');
                });
            }
        }
    };
    //  errorService_second.$inject = [];
    angular.module('_raiffisenApp').service('errorService_second', errorService_second);
}());