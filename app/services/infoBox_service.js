(function(){
    /**
     * Especialy for smart form icon.
     * In label where is input and select on element i must add .for_infobox
     */
    var infoboxService = function(){
        /**
         *
         */
        this.set_infoBox = function(){
            var help_id = 0;
            $('body').on('mouseenter', '.for_infobox', function(){
                var id =  Math.floor(Math.random() * 99999999999999);
                help_id= id;
                if($('body').find('#id_'+id).length>0){
                    return false;
                }
                var offset = $(this).offset(), lset = offset.left, tset = offset.top, title = $(this).attr('data-boxinfo');
                var tem = '<div  class="myinfoBox" style="left:'+lset+'px;top:'+tset+'px;z-index:3000" id="id_'+id+'">' +
                    title +
                    '</div>';
                $('body').append(tem);
                var mLeft = $('#id_'+id).width() / 2;
                $('#id_'+id).css('margin-left','-'+mLeft+'px');
                $('#id_'+id).animate({transform: 'scale(1,1)', opacity:0.8}, 800, 'easeOutBounce');
            }).on('mouseleave', '.for_infobox', function(){
                $('#id_'+help_id).remove();
            });
        };

        this.destroy_infoBox = function(){
            $('body').off('mouseenter mouseleave', '.for_infobox');
        };

    };


    angular.module('_raiffisenApp').service('infoboxService', infoboxService);
}());