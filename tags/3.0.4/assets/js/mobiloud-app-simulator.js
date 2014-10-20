jQuery(document).ready(function() {
    jQuery(".ml-iframe").on('load', function() {
        jQuery(".ml-loader").hide();
        jQuery(".ml-iframe").show();
    });
});