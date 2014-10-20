<script>
    jQuery(function() {
        var ml_allow_initial_close = false;
        jQuery( "#ml-initial-details" ).dialog({
          dialogClass: 'ml-initial-details-dialog wp-dialog',
          modal: false,
          width: 500,
          title: 'Your details',
          beforeClose: function() {
              return ml_allow_initial_close;
          },
          buttons: [
              {
                  text: "Save account details to continue",
                  class: 'button-primary',
                  click: function() {
                      var ml_name = jQuery("#ml-user-name").val();
                      var ml_email = jQuery("#ml-user-email").val();
                      var ml_site = jQuery("#ml-user-site").val();
                      var ml_maillist = jQuery("#ml-join-mailinglist").is(':checked');
                      
                      if(ml_name.length <= 0 || ml_email.length <= 0 || ml_site.length <= 0) {
                          alert('You must enter all the details');
                          return false;
                      } else {
                          var data = {
                              action: "ml_save_initial_data",
                              ml_name: ml_name,
                              ml_email: ml_email,
                              ml_site: ml_site,
                              ml_maillist: ml_maillist
                          };
                          jQuery.post(ajaxurl, data, function(response) {
                              ml_allow_initial_close = true;
                              jQuery( "#ml-initial-details" ).dialog( "close" );
                          });
                          
                          if(ml_maillist) {
                            var drip_data = {
                                fields: {
                                    email: ml_email
                                }
                            };
                            jQuery.post('https://www.getdrip.com/forms/7486138/submissions', drip_data, function(response) {
                            });
                          }
                      }
                  }
              }
          ]         
        });
    });
</script>
<div id='ml-initial-details' style="display:none;" class="ui-dialog">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row">Your name</th>
                <td>
                    <input size="36" type="text" id="ml-user-name" name="contactName" placeholder="Enter your name" value='<?php echo Mobiloud::get_option('ml_user_name', $current_user->display_name); ?>' required>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Your email</th>
                <td>
                    <input size="36" type="email" id="ml-user-email" name="email" placeholder="Enter your email" value='<?php echo Mobiloud::get_option('ml_user_email', $current_user->user_email); ?>' required>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Your website</th>
                <td>
                    <input size="36" type="text" id="ml-user-site" name="website" placeholder="Enter your website" value='<?php echo Mobiloud::get_option('ml_site_url', get_site_url()); ?>'>
                </td>
            </tr>
            <tr>
                <td colspan='2' class='ml-maillist-block'>
                    <input type='checkbox' id='ml-join-mailinglist' name='ml-join-mailinglist' value='true' checked/>
                    <p>Join the <strong>App Success Academy</strong> and learn all about designing, launching and promoting mobile apps to success. As always, we respect your privacy, and you can unsubscribe at any time.</p>
                </td>
            </tr>
            <tr>
                <td colspan='2'>
                    <small>By using Mobiloud you agree to Mobiloud's <a target="_blank" href="http://www.mobiloud.com/terms/">Terms of service</a> and <a href="http://www.mobiloud.com/privacy/">Privacy policy</a> </small>
                </td>
            </tr>
        </tbody>
    </table>
</div>