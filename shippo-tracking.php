<?php

/**
 * Plugin Name: Shippo Tracker
 * Plugin URI: https://ogunniyijulius.com
 * Description: Track shipments from multiple carriers using Shippo API
 * Version: 1.0.1
 * Author: Julius Ogunniyi
 * Author URI: https://ogunniyijulius.com
 * Text Domain: wp-shippo-tracker
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('shippo_tracker_activate')) {
    function shippo_tracker_activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'shippo_tracking_records';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                tracking_number VARCHAR(50) NOT NULL,
                carrier VARCHAR(50) NOT NULL,
                status VARCHAR(255) NOT NULL,
                tracked_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) $charset_collate;";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        }
    }

    register_activation_hook(__FILE__, 'shippo_tracker_activate');
}


define('WST_VERSION', '1.0.0');
define('WST_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WST_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once WST_PLUGIN_DIR . 'includes/shortcodes.php';
require_once WST_PLUGIN_DIR . 'includes/class-wp-shippo-tracker.php';
require_once WST_PLUGIN_DIR . 'includes/admin-settings.php';
require_once WST_PLUGIN_DIR . '/includes/ajax-handler.php';


// Initialize the plugin
function init_shippo_tracker() {
    return WP_Shippo_Tracker::get_instance();
    
}

add_action('plugins_loaded', 'init_shippo_tracker');

