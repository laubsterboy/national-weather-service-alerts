<?php

require_once('simply-lightbox-globals.php');

// if uninstall not called from WordPress then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

// For Single Site
if (!is_multisite()) {
    NWS_Alert_Globals::delete_options();
} else {
    global $wpdb;
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    $original_blog_id = get_current_blog_id();
    foreach($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        NWS_Alert_Globals::delete_options();
    }
    switch_to_blog($original_blog_id);
}
