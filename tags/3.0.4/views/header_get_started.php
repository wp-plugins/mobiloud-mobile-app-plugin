<div id="ml-sub-header">
    <div class="ml-intro-text">Use the plugin to <strong>customize and manage your own app</strong>. You can test your app for free and <a href="http://www.mobiloud.com/pricing/" target="_blank">sign up</a> when you're ready to publish it your app.</p>
        <p>For more information on Mobiloud, read <a href="http://www.mobiloud.com/how-it-works/" target="_blank">How It Works</a>, our <a href="http://www.mobiloud.com/features/" target="_blank">Features Tour</a> and for any questions see the <a href="http://www.mobiloud.com/help/" target="_blank">Help &amp; Support site</a>.</p>
    </div>
    <?php if(strlen(Mobiloud::get_option('ml_pb_app_id')) <= 0 && strlen(Mobiloud::get_option('ml_pb_secret_key')) <= 0): ?>
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
        <p>Any questions? <a class="ml-intercom" href="mailto:h89uu5zu@incoming.intercom.io">Contact Us</a></p>
    </div>
    <?php endif; ?>
</div>
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
</h2>