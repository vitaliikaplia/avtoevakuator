/**
 * document ready
 */
(function ($) {
    $(document).ready(function () {

        /** cookie popup */
        if(!$.cookie("user-cookies-accepted") && $('.cookiePopupWrapper').length){
            setTimeout(function(){
                $('*').blur();
                $('.cookiePopupWrapper').addClass('show');
            }, 3000);
            $('.cookiePopupWrapper .overallButton').click(function(){
                $.cookie("user-cookies-accepted", true, cookieParamsAdd);
                $('.cookiePopupWrapper').removeClass('show');
                return false;
            });
        }

        /** header */
        $("body").headroom({
            tolerance: {
                up: 14,
                down: 26,
            }
        });

        /** desktop current menu items */
        $('header .menu > li').each(function(){
            let thisLiEl = $(this),
                thisAel = thisLiEl.find('> a'),
                thisHref = thisAel.attr('href');
            thisLiEl.removeClass('current-menu-item');
            if(thisHref == window.location.href){
                thisLiEl.addClass('current-menu-item');
            }
        });
        // $('header .menus .main > li > ul > li').each(function(){
        //     let thisLiEl = $(this),
        //         thisAel = thisLiEl.find('> a'),
        //         thisHref = thisAel.attr('href');
        //     if(thisHref == window.location.href){
        //         thisLiEl.parent().parent().addClass('current-menu-ancestor');
        //     }
        // });

    });
})(jQuery);
