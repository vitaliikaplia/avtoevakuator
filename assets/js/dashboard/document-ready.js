/**
 * document ready
 */
(function ($) {
    $(document).ready(function () {

        if($('.custom-options-form').length){
            $(window).trigger("custom-options-form");
            $('.custom-options-form').on("change", function(e){
                $(window).trigger("custom-options-form");
            });
        }

        if($('.inside .mailPreview').length){
            $('.inside .mailPreview').on('load', function() {
                try {
                    var iframe = $(this)[0]; // DOM-елемент
                    var contentHeight = iframe.contentWindow.document.body.scrollHeight;
                    $(this).height(contentHeight);
                } catch (e) {
                    console.warn('Не вдалося отримати висоту iframe (можливо, інший домен).');
                }
            });
        }

    });
})(jQuery);
