jQuery(document).ready(function() {
    jQuery("input[name='ml_show_email_contact_link']").click(function() {
        if(jQuery(this).is(':checked')) {
            jQuery('.ml-email-contact-row').show();
        } else {
            jQuery('.ml-email-contact-row').hide();
        }
    });
});