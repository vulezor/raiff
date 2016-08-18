var mainService = function(){
    this.submenuAaction = function(){
        $('.menu_a').on('mouseenter', '>li', function(){
            if($(this).is(':not(:last-child)')){
                var header_title = $(this).children().eq(0).find('span').html();
                if($(this).children().length === 2){

                    $(this).children().eq(1).addClass("block_visible");
                    $(this).children().eq(0).after( '<div class="submenu_header">'+header_title+'</div>' );
                } else{
                    $(this).children().eq(0).after( '<div class="submenu_header">'+header_title+'</div>' );
                }
            }
        }).on('mouseleave', '>li', function(){
            $('.submenu_header').remove();
            $(this).children().eq(1).removeClass("block_visible");
        });
    };

    //---------------------------------------------------------------------------------------------------------------------------------

    this.submenuBaction = function(){
        $('.menu_b').on('mouseup', '>li>a', function(){
            if($(this).parent().is(':not(:last-child)')){
                $(this).parents('ul').children().each(function(i, item){
                    $(this).find('ul').slideUp( "fast");
                });
                if($(this).parent().find('ul').is(':visible')){
                    $(this).parent().find('ul').slideUp( "fast")
                } else {
                    $(this).parent().find('ul').slideDown( "fast")
                }

            } else {
                $(this).parents('ul').children().each(function(i, item){
                    $(this).find('ul').hide();
                });
            }
        });
    };

    this.domainURL = function(){
        return 'http://raiffagro.dev/';   // http://raiffeisenagro.otkupsirovina.com/http://agro.dev/
    }
};

angular.module('_raiffisenApp')
    .service('mainService', mainService);