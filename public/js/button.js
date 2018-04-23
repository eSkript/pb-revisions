(function ($) {
    $( document ).ready(function() {
        if(PBRevisionsButton.is_on){
            $(".a11y-toolbar ul").append('<li><a href="'+PBRevisionsButton.hide_url+'" role="button" title="'+PBRevisionsButton._deactivatePreview+'"><span class="dashicons dashicons-controls-play"></span></a></li>');
            $("body").prepend('<div class="notice">'+PBRevisionsButton._previewNotice+' <a href="'+PBRevisionsButton.hide_url+'" role="button" title="'+PBRevisionsButton._deactivatePreview+'">'+PBRevisionsButton._deactivate+'</a></div>');
        }else {
            $(".a11y-toolbar ul").append('<li><a href="'+PBRevisionsButton.show_url+'" role="button" title="'+PBRevisionsButton._showPreview+'"><span class="dashicons pbricons-revision"></span></a></li>');
        }
    });
})(jQuery);