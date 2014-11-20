jQuery(document).ready(function() {
    jQuery(".sim-btn").click(function() {
        var url = jQuery(this).attr('href');
        var new_url = url.substring(0, url.indexOf('&TB_iframe') !== -1 ? url.indexOf('&TB_iframe') : url.length);
        jQuery(this).attr('href', new_url
                + '&TB_iframe=true&width=650&height='+(jQuery(window).height()-(jQuery(window).width()>850?60:20)));
    });  
        
        
    jQuery(".ml-iframe").on('load', function() {
        jQuery(".ml-loader").hide();
        jQuery(".ml-iframe").show();
    });
});