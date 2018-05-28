(function ($) {
    $( document ).ready(function() {
        if(PBRevisionsButton.is_on){
            $(".a11y-toolbar ul").append('<li><a href="'+PBRevisionsButton.hide_url+'" role="button" title="'+PBRevisionsButton._deactivatePreview+'" class="pb_revisions_toggle_button--on"><span class="dashicons pbricons-revision pbricons-revision--white"></span></a></li>');
            $("body").prepend('<div class="notice">'+PBRevisionsButton._previewNotice+'</div>');
        }else {
            $(".a11y-toolbar ul").append('<li><a href="'+PBRevisionsButton.show_url+'" role="button" title="'+PBRevisionsButton._showPreview+'"><span class="dashicons dashicons-controls-play"></span></a></li>');
        }
    });
})(jQuery);