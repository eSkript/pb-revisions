(function ($) {
    $( document ).ready(function() {
        if(PBRevisionsButton.is_on){
            $(".a11y-toolbar ul").append('<li><a href="'+PBRevisionsButton.hide_url+'" role="button" title="Show Active Version"><span class="dashicons dashicons-controls-play"></span></a></li>');
            $("body").prepend('<div class="notice">You currently see a preview. Your readers see an other version. <a href="'+PBRevisionsButton.hide_url+'" role="button" title="Show active Version">Deactivate</a></div>');
        }else {
            $(".a11y-toolbar ul").append('<li><a href="'+PBRevisionsButton.show_url+'" role="button" title="Show Working Version"><span class="dashicons pbricons-revision"></span></a></li>');
        }
    });
})(jQuery);