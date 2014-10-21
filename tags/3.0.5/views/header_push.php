<h2 class="nav-tab-wrapper ml-push-tabs">
    <?php foreach(Mobiloud_Admin::$push_tabs as $tab_key=>$tab_name): ?>
    <?php
    $active_tab = '';
    if((!isset($_GET['tab']) && $tab_key == 'notifications') || (isset($_GET['tab']) && $_GET['tab'] == $tab_key)) {
        $active_tab = 'nav-tab-active';
    }
    ?>
    <a class="nav-tab <?php echo $active_tab; ?>" href="<?php echo admin_url('admin.php?page=mobiloud_push&tab=' . $tab_key); ?>"><?php echo esc_html($tab_name); ?></a>
    <?php endforeach; ?>
</h2>