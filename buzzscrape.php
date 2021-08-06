<?php
/**
 * Plugin Name: Buzzsprout Scraper
 * Description: Provides a widget listing the podcast channels available from Buzzsprout
 * Version: 0.9.0
 * Author: Derek Muico
 */

require_once(__DIR__ . '/bzs_widget.php');
require_once(__DIR__ . '/cache.php');

// Register dependencies
function bzs_enqueue_scripts() {
    $ver = '0.9.0-9';
    
    wp_enqueue_style('bzs_style', plugins_url('css/style.css', __FILE__), array(), $ver);
}

add_action('wp_enqueue_scripts', 'bzs_enqueue_scripts');

// Register dependencies
function bzs_admin_enqueue_scripts() {
    $ver = '0.9.0-9';
    
    wp_register_script(
        'bzs_script_admin', plugins_url('js/admin.js', __FILE__), array('jquery'), $ver
    );

    if (current_user_can('editor') || current_user_can('administrator')) {
        wp_enqueue_script('bzs_script_admin'); // for async cache refresh
    }
}

add_action('admin_enqueue_scripts', 'bzs_admin_enqueue_scripts');

// Load widget
function bzs_load() {
    register_widget('BZS_Widget');
}

// Register the widget
add_action('widgets_init', 'bzs_load');

// Refresh all accounts
function bzs_refresh_all() {
    if (!current_user_can('editor') && !current_user_can('administrator')) {
        return;
    }

    if (get_option('bzs_auto_refresh') === 'on') {
        $ids = bzs_get_ids();
    
        foreach ($ids as $row) {
            bzs_refresh($row->bid);
        }
    }
}

add_action('bzs_daily_refresh', 'bzs_refresh_all');

function bzs_ajax_autorefresh() {
    $valid = current_user_can('editor') || current_user_can('administrator');
    $valid = $valid && isset($_POST['autorefresh']);

    if (!$valid) {
        wp_send_json_error('Access denied.');
        return;
    }

    update_option('bzs_auto_refresh', $_POST['autorefresh']);
    wp_send_json_success();
}

// Register admin action
add_action('wp_ajax_bzs_autorefresh', 'bzs_ajax_autorefresh');

// Plugin activation
function bzs_activate() {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    bzs_create_table();
    update_option('bzs_auto_refresh', 'on');

    if (!wp_next_scheduled('bzs_daily_refresh') ) {
        wp_schedule_event(time(), 'daily', 'bzs_daily_refresh');
    }
}

register_activation_hook(__FILE__, 'bzs_activate');

// Plugin deactivation
function bzs_deactivate() {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    wp_clear_scheduled_hook('bzs_daily_refresh');
    bzs_delete_table();
}

register_deactivation_hook( __FILE__, 'bzs_deactivate' );