<?php if(strlen(Mobiloud::get_option('ml_pb_app_id')) <= 0 && strlen(Mobiloud::get_option('ml_pb_secret_key')) <= 0): ?>
<div id="ml-sub-header">
    <div class="ml-intro-text">
		<p>
		Mobiloud is the easy solution to publish your own mobile apps. Start by customizing the design, then configure the menu and test a live preview of your app.</p>
        <p>For more information, see <a href="http://www.mobiloud.com/help/knowledge-base/customize/?utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=plugin-admin-get-started<?php echo get_option('affiliate_link', null); ?>" target="_blank">How To Get Started</a> (video), our <a href="http://www.mobiloud.com/features/?utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=plugin-admin-get-started<?php echo get_option('affiliate_link', null); ?>
" target="_blank">Features page</a> and for any questions <a class="ml-intercom" href="mailto:h89uu5zu@incoming.intercom.io">contact our support team</a> (or check out our <a href="http://www.mobiloud.com/help/?utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=plugin-admin-get-started<?php echo get_option('affiliate_link', null); ?>
" target="_blank">Help and Support site</a>).</p>
    </div>
    <div class="ml-task-list">
        <h3>Get Started Here</h3>
        <ul>
            <?php 
			$tn=1;
			foreach(Mobiloud_Admin::get_started_tasks() as $task_key=>$task): ?>
            <li class="<?php echo Mobiloud_Admin::get_task_class($task_key); ?>">
                <span class="task-icon"></span>
                <?php echo $tn.") ";?><a href="<?php echo admin_url('admin.php?page=mobiloud&tab=' . $task_key); ?>"><?php echo $task['task_text']; $tn++; ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
		
    </div>
</div>
<?php endif; ?>

<h2 class="nav-tab-wrapper get-started-tabs">
    <?php foreach(Mobiloud_Admin::get_started_tasks() as $task_key=>$task): ?>
    <?php
    $active_task = '';
    if((!isset($_GET['tab']) && $task_key == 'design') || (isset($_GET['tab']) && $_GET['tab'] == $task_key)) {
        $active_task = 'nav-tab-active';
    }
    ?>
    <a class="nav-tab <?php echo $active_task; ?>" href="<?php echo admin_url('admin.php?page=mobiloud&tab=' . $task_key); ?>"><?php echo esc_html($task['nav_text']); ?></a>
    <?php endforeach; ?>
	<a href="<?php echo admin_url('admin.php?page=mobiloud_settings'); ?>" class="more-button">More Settings</a>
	<a class="ml-intercom more-button" href="mailto:h89uu5zu@incoming.intercom.io">Contact Support</a>
	
</h2>