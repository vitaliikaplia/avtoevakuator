//@prepros-prepend plugins/select2.min.js

/**
 * document ready
 */
(function ($) {
    $(document).ready(function () {

        /**
         * tweaks multi select
         */
        if($('.custom-options-select-multiple').length){
            $('.custom-options-select-multiple').each(function(){
                $(this).select2({
                    closeOnSelect: false,
                    width: '100%'
                });
            });
        }

        /**
         * code fields
         */
        if($('.custom-options-code').length){
            $('.custom-options-code').each(function(){
                let editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
                wp.codeEditor.initialize( $(this), editorSettings );
            });
        }

        /**
         * range
         */
        if($('.custom-options-range').length){
            $('.custom-options-range').each(function(){
                const thisEl = $(this);
                const thisName = thisEl.attr('name');
                const inputEl = thisEl.parent().find('.val_'+thisName+'_display');
                thisEl.on('input change', function() {
                    inputEl.val(thisEl.val());
                });
                inputEl.on('input', function() {
                    thisEl.val($(this).val());
                });
                inputEl.on('blur', function() {
                    inputEl.val(thisEl.val());
                });
            });
        }

        if($('.custom-button-link-button').length){
            $('.custom-button-link-button').each(function(){
                const thisButtonEl = $(this);
                const linkInputHtml = thisButtonEl.prev();
                const removeLinkButton = thisButtonEl.parent().find('.remove-link-button');
                const previewLinkWrapper = thisButtonEl.parent().find('.linkPreview');
                const linkInputTitle = thisButtonEl.parent().find('#'+thisButtonEl.attr('id')+'_title');
                const linkInputUrl = thisButtonEl.parent().find('#'+thisButtonEl.attr('id')+'_url');
                const linkInputTarget = thisButtonEl.parent().find('#'+thisButtonEl.attr('id')+'_target');
                thisButtonEl.on('click', function() {
                    wpActiveEditor = true;
                    wpLink.open(linkInputHtml.attr('id'));
                    return false;
                });
                $('body').on('click', '#wp-link-submit', function(event) {
                    if($('#wp-link-text').val()){
                        linkInputTitle.val($('#wp-link-text').val());
                        previewLinkWrapper.find('.title').addClass('show').html($('#wp-link-text').val());
                    } else {
                        linkInputTitle.val('');
                        previewLinkWrapper.find('.title').removeClass('show').html('');
                    }
                    if($('#wp-link-url').val()){
                        linkInputUrl.val($('#wp-link-url').val());
                        previewLinkWrapper.find('.url').addClass('show').html($('#wp-link-url').val());
                        thisButtonEl.addClass('hide');
                        previewLinkWrapper.addClass('show');
                    } else {
                        linkInputUrl.val('');
                        previewLinkWrapper.find('.url').removeClass('show').html('');
                        thisButtonEl.removeClass('hide');
                        previewLinkWrapper.removeClass('show');
                    }
                    if($('#wp-link-target').prop('checked')){
                        previewLinkWrapper.find('.target').addClass('show').html(previewLinkWrapper.find('.target').attr('data-localized'));
                    } else {
                        previewLinkWrapper.find('.target').removeClass('show').html('');
                    }
                    linkInputTarget.val($('#wp-link-target').prop('checked') ? '_blank' : '');
                    wpLink.textarea = $('body');
                    wpLink.close();
                    event.preventDefault ? event.preventDefault() : event.returnValue = false;
                    event.stopPropagation();

                    return false;
                });
                removeLinkButton.on('click', function(){
                    linkInputTitle.val('');
                    previewLinkWrapper.find('.title').removeClass('show').html('');
                    linkInputUrl.val('');
                    previewLinkWrapper.find('.url').removeClass('show').html('');
                    linkInputTarget.val('');
                    previewLinkWrapper.find('.target').removeClass('show').html('');
                    previewLinkWrapper.removeClass('show');
                    thisButtonEl.removeClass('hide');
                });
            });
        }

    });
})(jQuery);
