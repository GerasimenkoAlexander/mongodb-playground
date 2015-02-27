;(function($){
    function inIframe () {
        try {
            return window.self !== window.top;
        } catch (e) {
            return true;
        }
    }
    if(inIframe()){
        $('body').addClass('iframe');
    }
})(jQuery);