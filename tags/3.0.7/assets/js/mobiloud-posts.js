jQuery(document).ready(function() {
    jQuery("input[name='ml_comments_system']").click(function() {
        var sys = jQuery("input[name='ml_comments_system']:checked").val();
        if(sys === 'disqus') {
            jQuery(".ml-disqus-row").show();
        } else {
            jQuery(".ml-disqus-row").hide();
        }
    });
});