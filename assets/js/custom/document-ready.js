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

        /** mobile menu */
        $('.burger').click(function(){
            if(!$('body').hasClass('side-menu-opened')){
                $('body').addClass('side-menu-opened');
            } else {
                $('body').removeClass('side-menu-opened');
            }
        });

        /** desktop current menu items */
        $('header .menu > li,.nobileMenu .menu > li').each(function(){
            let thisLiEl = $(this),
                thisAel = thisLiEl.find('> a'),
                thisHref = thisAel.attr('href');
            thisLiEl.removeClass('current-menu-item');
            if(thisHref == window.location.href){
                thisLiEl.addClass('current-menu-item');
            }
        });

        /** simple swiper slider */
        if($('.customBlockWrapper.main-first-screen').length){
            $('.customBlockWrapper.main-first-screen').each(function(){
                run_simple_swiper_slider($(this));
            });
        }

        /** reviews swiper slider */
        if($('.customBlockWrapper.main-reviews').length){
            $('.customBlockWrapper.main-reviews').each(function(){
                run_reviews_swiper_slider($(this));
            });
        }

        /** contacts form phone mask */
        if($('input.phone').length){
            $('input.phone').each(function(){
                let thisPhoneInput = $(this);
                if(thisPhoneInput.length){
                    thisPhoneInput.mask('+380 00 000-00-00', {placeholder: $(this).attr('placeholder')});
                    thisPhoneInput.on('focus', function(){
                        if($(this).val() == ""){
                            $(this).val("+380 ");
                        }
                    }).on('blur', function(){
                        if($(this).val() && ($(this).val().trim() == "+380" || $(this).val() == "+38" || $(this).val() == "+3" || $(this).val() == "+")){
                            $(this).val("");
                        }
                    });
                }
            });
        }

        /** textarea autogrow */
        if($('textarea').length){
            $('textarea').each(function(){
                $(this).autogrow();
            });
        }

        /** contacts form */
        if($('form.contact').length){
            $('form.contact').each(function(){

                let form = $(this);

                form.find('input,textarea').on('input', function(){
                    $(this).removeClass('red');
                });

                form.submit(function(e){

                    e.preventDefault();
                    let readyToSend = true;

                    form.find('input[name="user_full_name"]').each(function(){
                        if($(this).val().trim() == ""){
                            readyToSend = false;
                            form.find('input[name="user_full_name"]').addClass('red');
                        } else if($(this).val().trim().length < 5){
                            readyToSend = false;
                            form.find('input[name="user_full_name"]').addClass('red');
                        } else if($(this).val().trim().length > 88){
                            readyToSend = false;
                            form.find('input[name="user_full_name"]').addClass('red');
                        }
                    });

                    form.find('input[name="user_phone"]').each(function(){
                        if($(this).val().trim() != "" && $(this).val().trim().length != 17){
                            readyToSend = false;
                            form.find('input[name="user_phone"]').addClass('red');
                        }
                    });

                    form.find('input[name="user_email"]').each(function(){
                        if($(this).val().trim() == ""){
                            readyToSend = true;
                        } else if($(this).val().trim() != "" && !isValidEmailAddress($(this).val().trim())){
                            readyToSend = false;
                            form.find('input[name="user_email"]').addClass('red');
                        } else if($(this).val().trim() != "" && $(this).val().trim().length < 5){
                            readyToSend = false;
                            form.find('input[name="user_email"]').addClass('red');
                        } else if($(this).val().trim() != "" && $(this).val().trim().length > 58){
                            readyToSend = false;
                            form.find('input[name="user_email"]').addClass('red');
                        }
                    });

                    // form.find('textarea').each(function(){
                    //     if($(this).val().trim() == ""){
                    //         readyToSend = false;
                    //         form.find('textarea').addClass('red');
                    //     } else if($(this).val().trim().length < 5){
                    //         readyToSend = false;
                    //         form.find('textarea').addClass('red');
                    //     } else if($(this).val().trim().length > 58){
                    //         readyToSend = false;
                    //         form.find('textarea').addClass('red');
                    //     }
                    // });

                    if(readyToSend){

                        form.addClass("busy");

                        form.find('input, button').each(function(){
                            $(this).blur();
                        });

                        let formData = new FormData(form[0]);
                        formData.append('current_page', window.location.href);

                        $.ajax({
                            type: "POST",
                            url: ajaxUrl,
                            dataType: 'json',
                            data: formData,
                            contentType: false,
                            cache: false,
                            processData: false,
                            success : function (out) {
                                form.removeClass("busy");
                                form.trigger("reset");
                                form.parent().append('<div class="success" style="display: none;"></div>');
                                form.parent().find('.success').append('<p class="successTitle">'+out.data.title+'</p>');
                                form.parent().find('.success').append('<p class="successMessage">'+out.data.message+'</p>');
                                form.parent().find('.success').slideDown(400);
                                setTimeout(function(){
                                    form.parent().find('.success').slideUp(400, function(){
                                        $(this).remove();
                                    });
                                }, 6000);
                            }
                        });

                    }

                });

            });
        }

    });
})(jQuery);
