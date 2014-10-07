<?php
add_action('wp_ajax_ml_push_notification_manual_send', 'ml_push_notification_manual_send_callback');
add_action('wp_ajax_ml_push_notification_history', 'ml_push_notification_history');
add_action('wp_ajax_ml_push_notification_check_duplicate', 'ml_push_notification_check_duplicate');

function mobiloud_push_notifications_page() {
    include(dirname( __FILE__ ).'/menu_page.php');
}

function ml_push_notification_manual_send_callback()
{
	if(isset($_POST['ml_push_notification_msg']))
	{
        $platform = array();
        switch($_POST['ml_push_notification_os']) {
            case 'all':
                $platform = array(0,1);
            break;
            case 'android':
                $platform = array(1);
            break;
            case 'ios':
                $platform = array(0);
            break;
        }
        $tags = array();
        $tagNames = array();
        $postId = null;
        if(strlen($_POST['ml_push_notification_data_id']) > 0) {
            if(strpos($_POST['ml_push_notification_data_id'], 'custom') !== false) {
                $postId = $_POST['ml_push_notification_post_id'];
            } else {
                $postId = substr($_POST['ml_push_notification_data_id'], 8);
            }
        }
        if($postId != null) {
            $tags = ml_get_post_tag_ids($postId);
            $tagNames = ml_get_post_tags($postId);
        }
        $tags[] = 'all';
        $tagNames[] = 'all';
        $payload = array();
        if($postId !== null) {
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $postId ), 'single-post-thumbnail' );
            $payload = array(
                'post_id'=>$postId,                
            );
            if(is_array($image)) {
                $payload['featured_image'] = $image[0];
            }
        } 
        $data = array(            
            'platform'=>$platform,
            'msg'=>trim($_POST['ml_push_notification_msg']),
            'sound'=>'default',
            'badge'=>null,
            'notags'=>true,
            'tags'=>$tags,
            'payload'=>$payload
        );
		ml_pb_send_batch_notification($data, $tagNames);
        Mobiloud::track_user_event('push_notification');
	}

	ml_push_notification_manual_send();
	
	die();
}
    
function ml_push_notification_manual_send_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action: 'ml_push_notification_manual_send'
		};
		jQuery("#ml_push_notification_manual_send").css("display","none");
			
		$.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_push_notification_manual_send").html(response).show();
		});			
			
	});
	</script>
	<?php
}

function ml_push_notification_history_ajax_load()
{
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		loadNotificationHistory();		
			
	});
    
    var loadNotificationHistory = function() {
        var data = {
			action: 'ml_push_notification_history'
		};
		jQuery("#ml_push_notification_history").css("display","none");
			
		jQuery.post(ajaxurl, data, function(response) {
			//saving the result and reloading the div
			jQuery("#ml_push_notification_history").html(response).show();
		});	
    };
	</script>
	<?php
}

function ml_push_notification_chart() {
    $notifications = ml_notifications(100);
    
    if(is_array($notifications) && count($notifications) > 0) {
        ?>

        <script type="text/javascript">
         google.load("visualization", "1", {packages:["corechart"], callback: drawChart});

          function drawChart() {
            <?php ml_push_notification_chart_data(); ?>


            var options = {
              title: 'Latest Notifications',
              hAxis: {title: 'Notifications'}
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('notifications_chart'));
            chart.draw(data, options);
          }
        </script>
        <div id="notifications_chart" style="width: 100%; height: 200px; margin: 0 auto; margin-bottom: 20px;"></div>
        <?Php
    }
}

function ml_push_notification_chart_data() {
    $data = 'var data = google.visualization.arrayToDataTable([
        [\'Date\', \'Count\'],';
            
    $notifications = ml_notifications(100);
    $dates = array();
    if(count($notifications)) {
        foreach($notifications as $notification) {
            if(date('mY') === date('mY', $notification->time)) {
                //same month so group by day
                $dates[date('d M Y', $notification->time)] += 1;
            } else {
                $dates[date('M Y', $notification->time)] += 1;
            }
        }
    }
    $dates = array_reverse($dates);
    foreach($dates as $date=>$count) {
        $data .= '[\''.$date.'\', '.$count.'],';
    }
    $data = rtrim($data, ",");
    $data .= ']);';
    echo $data;
}

function ml_push_notification_history() {
    ml_push_notification_chart();
    ?>
    <table class="wp-list-table widefat fixed posts">
        <thead>
            <tr>
                <th scope="col" id="time" class="manage-column column-time">Sent</th>
                <th scope="col" id="message" class="manage-column column-message">Message</th>
                <th scope="col" id="attachment" class="manage-column column-attachment">Attachment</th>
                <th scope="col" id="platform" class="manage-column column-platform" style="">Platform</th>
                <th scope="col" id="tags" class="manage-column column-tags" style="">Tags</th>
            </tr>
        </thead>

        <tfoot>
           <tr>
                <th scope="col" id="time" class="manage-column column-time">Sent</th>
                <th scope="col" id="message" class="manage-column column-message">Message</th>
                <th scope="col" id="attachment" class="manage-column column-attachment">Attachment</th>
                <th scope="col" id="platform" class="manage-column column-platform" style="">Platform</th>
                <th scope="col" id="tags" class="manage-column column-tags" style="">Tags</th>
            </tr>
        </tfoot>

        <tbody id="the-list">
            <?php $notifications = ml_notifications(100); ?>
            <?php if(count($notifications)): ?>
                <?php foreach($notifications as $notification): ?>
                <?php
                $notificationPlatform = '';
                if($notification->android == 'Y' && $notification->ios == 'Y') {
                    $notificationPlatform = 'All';
                } elseif($notification->android == 'Y') {
                    $notificationPlatform = 'Android';
                } elseif($notification->ios == 'Y') {
                    $notificationPlatform = 'iOS';
                }
                ?>
                <tr id="notification-<?php echo $notification->id; ?>">
                    <td class="column-time"><?php echo date('d/m/Y H:i:s', $notification->time); ?></td>
                    <td class="column-time"><?php echo $notification->msg; ?></td>
                    <td class="column-time"><?php echo $notification->post_id > 0 ? '{post_id:'.$notification->post_id.'}' : ''; ?></td>
                    <td class="column-time"><?php echo $notificationPlatform; ?></td>
                    <td class="column-time"><?php echo ml_tags_to_labels($notification->tags); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No notifications found.</td>
                </tr>
            <?php endif; ?>
        </tbody>                  
    </table>
    <?php
    exit;
}

function ml_push_notification_check_duplicate() {
    $postId = null;
    $android = null;
    $ios = null;
    
    if(strlen($_POST['ml_push_notification_data_id']) > 0) {
        if(strpos($_POST['ml_push_notification_data_id'], 'custom') !== false) {
            $postId = $_POST['ml_push_notification_post_id'];
        } else {
            $postId = substr($_POST['ml_push_notification_data_id'], 8);
        }
    }
    
    switch($_POST['ml_push_notification_os']) {
        case 'all':
            $android = 'Y';
            $ios = 'Y';
        break;
        case 'android':
            $android = 'Y';
            $ios = 'N';
        break;
        case 'ios':
            $android = 'N';
            $ios = 'Y';
        break;
    }
    $notifications = ml_get_notification_by(array(
        'msg'=>trim($_POST['ml_push_notification_msg']),
        'post_id'=>$postId,
        'android'=>$android,
        'ios'=>$ios
    ));
    echo count($notifications) > 0 ? 'true' : '';
    exit;
}

function ml_push_notification_manual_send()
{

	ml_push_notification_manual_send_div();

	?>

	
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
        
        $("#ml_push_notification_msg").on("input", function () {
            limitChars(this, 107, 'ml_notification_chars');
        });
                
        jQuery("#ml_push_notification_data_id").change(function() {
            if($(this).val() === 'custom') {
                jQuery("#ml_push_notification_post_id_row").show();
            } else {
                jQuery("#ml_push_notification_post_id_row").hide();
            }
        });
        
		jQuery("#ml_push_notification_manual_send_submit").click(function(){
			
            if(validateNotification()) {            
                var checkDuplicate = checkDuplicateNotification();
                
                var cont = true;
                if(checkDuplicate) {
                    cont = confirm('It seems that you have sent this exact message already, are you sure you wish to send it again?');
                }
                
                if(cont) {
                    jQuery("#ml_push_notification_manual_send_submit").val("<?php _e('Sending...'); ?>");
                    jQuery("#ml_push_notification_manual_send_submit").attr("disabled", true);

                    jQuery("#ml_push_notification_manual_send").css("opacity","0.5");

                    var data = {
                        action: 'ml_push_notification_manual_send',
                        ml_push_notification_msg: jQuery("#ml_push_notification_msg").val(),
                        ml_push_notification_data_id: jQuery("#ml_push_notification_data_id").val(),
                        ml_push_notification_post_id: jQuery("#ml_push_notification_post_id").val(),
                        ml_push_notification_os: jQuery("input[name='ml_push_notification_os']:checked").val()
                    };

                    $.post(ajaxurl, data, function(response) {
                        //saving the result and reloading the div
                        jQuery("#ml_push_notification_manual_send").html(response).fadeIn();
                        jQuery("#ml_push_notification_manual_send_submit").val("<?php _e('Send'); ?>");
                        jQuery("#ml_push_notification_manual_send_submit").attr("disabled", false);
                        jQuery("#ml_push_notification_manual_send").css("opacity","1.0");
                        loadNotificationHistory();
                        jQuery("#success-message").show();
                        setTimeout(function(){
                            jQuery("#success-message").fadeOut();
                        }, 2000);
                    });		
                }
                return true;
			} else {
                return false;
            }
		});
        
        
	});
    
    var limitChars = function(txtMsg, CharLength, indicator){
        chars = txtMsg.value.length;
        document.getElementById(indicator).innerHTML = (CharLength - chars) + " character(s) left.";
        if (chars > CharLength) {
            txtMsg.value = txtMsg.value.substring(0, CharLength);
            //Text in textbox was trimmed, re-set indicator value to 0
            document.getElementById(indicator).innerHTML = "0 character(s) left.";
        }
    }

    var checkDuplicateNotification = function() {
        var data = {
            action: 'ml_push_notification_check_duplicate',
            ml_push_notification_msg: jQuery("#ml_push_notification_msg").val(),
            ml_push_notification_data_id: jQuery("#ml_push_notification_data_id").val(),
            ml_push_notification_post_id: jQuery("#ml_push_notification_post_id").val(),
            ml_push_notification_os: jQuery("input[name='ml_push_notification_os']:checked").val()
        };
        var duplicate = false;
        jQuery.ajax({
            url: ajaxurl,
            data: data,
            type: 'POST',
            async: false,
            success: function(response) {
                if(jQuery.trim(response).length > 0) {
                    duplicate = true;
                } 
            }
        });
        return duplicate;
    };
    
    var validateNotification = function() {
        var errors = [];
        
        var message = jQuery.trim(jQuery("#ml_push_notification_msg").val());
        if(message.length === 0) {
            errors.push('Message cannot be blank');
        }
        
        var attach = jQuery("#ml_push_notification_data_id").val();
        if(attach === 'custom') {
            var customPostID = jQuery.trim(jQuery("#ml_push_notification_post_id").val());
            if(customPostID.length === 0) {
                errors.push('Custom ID cannot be blank');
            } else if(!jQuery.isNumeric(customPostID)) {
                errors.push('Custom ID must be a number');
            }
        } 
        
        if(errors.length > 0) {
            jQuery("#error-message").html(errors.join("<br/>")).show();            
            return false;
        } else {
            jQuery("#error-message").hide();
            return true;
        }
    };
	</script>
	
	
	<?php
    
}

function ml_push_notification_manual_send_div()
{
	?>
    
    <div class="ml_send_notification_box">
        <div id="error-message" class="error" style="display: none;"></div>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope='row'>
                        <label for="ml_push_notification_msg">Message</label>
                    </th>
                    <td>
                        <input id="ml_push_notification_msg" placeholder="Your message" name="ml_push_notification_msg" type="text" style="width: 100%" class='regular-text'/>
                        <p id="ml_notification_chars" class="description">107 character(s) left.</p>
                    </td>
                </tr>
                <tr>
                    <th scope='row'>
                        <label for='ml_push_notification_data_id'>Attach</label>
                    </th>
                    <td>
                        <select id="ml_push_notification_data_id">
                            <option value=''>Select attachment...</option>
                            <?php $posts = get_posts(array('posts_per_page' => 10,'orderby' => 'post_date','order' => 'DESC','post_type' => 'post')); ?>
                            <?php $pages = get_pages(array('sort_order' => 'ASC', 'sort_column' => 'post_title', 'post_type' => 'page','post_status' => 'publish')); ?>
                            <optgroup label="Posts">
                            <?php foreach($posts as $post) { ?>

                            <option value="post_id-<?php echo $post->ID; ?>">
                            <?php if(strlen($post->post_title) > 40) { ?>

                            <?php echo substr($post->post_title,0,40); ?>

                            ..
                            <?php } else { ?>

                            <?php echo $post->post_title; ?>

                            <?php } ?>
                            </option><?php } ?>
                            </optgroup><optgroup label="Pages">
                            <?php foreach($pages as $page) { ?>

                            <option value="post_id-<?php echo $page->ID; ?>">
                            <?php if(strlen($page->post_title) > 40) { ?>

                            <?php echo substr($page->post_title,0,40); ?>

                            ..
                            <?php } else { ?>

                            <?php echo $page->post_title; ?>

                            <?php } ?>
                            </option><?php } ?>
                            </optgroup>
                            <optgroup label="Custom">
                                <option value="custom">Post/Page ID</option>
                            </optgroup>
                        </select>
                        <p class="description">You can attach a post or a page to your notification (optional).</p>
                    </td>
                </tr>
                <tr id="ml_push_notification_post_id_row" style="display: none;">
                    <th scope='row'>
                        <label for="ml_push_notification_post_id">Custom Post/Page ID</label>
                    </th>
                    <td>
                        <input id="ml_push_notification_post_id" placeholder="Custom ID" name="ml_push_notification_post_id" type="text" class='regular-text'/>
                    </td>
                </tr>
                <tr>
                    <th scope='row'>
                        <label>Send to Platform</label>
                    </th>
                    <td>
                        <p>
                            <?php 
                            $registeredDevicesCount = ml_registered_devices_count(); 
                            $total_count = 0;
                            $android_count = 0;
                            $ios_count = 0;
                            if($registeredDevicesCount['android'] !== null) {
                                $total_count += $registeredDevicesCount['android'];
                                $android_count = $registeredDevicesCount['android'];
                            }
                            if($registeredDevicesCount['ios'] !== null) {
                                $total_count += $registeredDevicesCount['ios'];
                                $ios_count = $registeredDevicesCount['ios'];
                            }
                            ?>
                            <label>
                                <input id="ml_push_notification_os_all" type="radio" name='ml_push_notification_os' value="all" checked/> All (<?php echo $total_count; ?> total devices)
                            </label><br/>
                            <label>
                                <input id="ml_push_notification_android" type="radio" name='ml_push_notification_os' value="android" /> Android only (<?php echo $android_count; ?> devices)
                            </label><br/>
                            <label>
                                <input id="ml_push_notification_ios" type="radio" name='ml_push_notification_os' value="ios" /> iOS only (<?php echo $ios_count; ?> devices)
                            </label>
                        </p>

                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" class='button button-primary button-large' id="ml_push_notification_manual_send_submit" value="<?php _e('Send'); ?>" />
        </p>
    </div><!--.ml_send_notification_box-->
	<?php
}

function ml_tags_to_labels($tags) {
    $labels = '';
    if(strlen($tags) > 0) {
        $tags = explode(",", $tags);
        foreach($tags as $tag) {
            $labels .= '<div class="tag-label info">'.$tag.'</div>';
        }
    }
    return $labels;
}

//check if pushbot license details are valid
function ml_check_pb_keys() {
    $headers = array(
        'X-PUSHBOTS-APPID' => get_option('ml_pb_app_id'),
        'X-PUSHBOTS-SECRET' => get_option('ml_pb_secret_key'),
        'Content-Type'=> 'application/json',
        'Content-Length'=> 0
    );
    $url = 'https://api.pushbots.com/analytics';
    
    $request = new WP_Http;
    $result = $request->get($url, array(
        'timeout' => 10,
        'headers' => $headers,
        'sslverify'=>false
    ));
    return isset($result['response']['code']) && $result['response']['code'] == 200;
}
?>