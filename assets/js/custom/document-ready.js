/**
 * document ready
 */
(function ($) {
    $(document).ready(function () {

        /** Cookie Pop-Up */
        if(!$.cookie("user-cookies-accepted") && $('.cookiePopupBg').length && $('.cookiePopupWrapper').length){
            setTimeout(function(){
                $('.cookiePopupWrapper').addClass('show');
                $('.cookiePopupBg').addClass('show');
            }, 3000);
            $('.cookiePopupWrapper a.close').click(function(){
                $.cookie("user-cookies-accepted", true);
                $('.cookiePopupWrapper').removeClass('show');
                $('.cookiePopupBg').removeClass('show');
                return false;
            });
        }

    });
})(jQuery);
