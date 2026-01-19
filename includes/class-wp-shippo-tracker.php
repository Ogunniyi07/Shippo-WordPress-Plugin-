<?php 

if (!defined('ABSPATH')) {
    exit;
}

// Include Admin Class
require_once plugin_dir_path(__FILE__) . 'admin-settings.php';

class WP_Shippo_Tracker {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Add AJAX handlers
        add_action('wp_ajax_track_shipment', array($this, 'ajax_track_shipment'));
        add_action('wp_ajax_nopriv_track_shipment', array($this, 'ajax_track_shipment'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'wp-shippo-tracker',
            WST_PLUGIN_URL . 'assets/css/front.css',
            array(),
            WST_VERSION
        );

        wp_enqueue_script(
            'wp-shippo-tracker',
            WST_PLUGIN_URL . 'assets/js/front.js',
            array('jquery'),
            WST_VERSION,
            true
        );

        wp_localize_script('wp-shippo-tracker', 'wpShippoTracker', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp-shippo-tracker-nonce')
        ));
    }

    public function create_tracking_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'shippo_tracking_records';
        
        $charset_collate = $wpdb->get_charset_collate();
    
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            tracking_number VARCHAR(50) NOT NULL,
            carrier VARCHAR(50) NOT NULL,
            status VARCHAR(255) NOT NULL,
            tracking_history TEXT NOT NULL,
            tracked_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";
    
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public function log_tracking_data($tracking_number, $carrier, $status, $tracking_history) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'shippo_tracking_records';
        // Insert into DB
        $inserted = $wpdb->insert(
            $table_name,
            [
                'tracking_number' => sanitize_text_field($tracking_number),
                'carrier' => sanitize_text_field($carrier),
                'status' => sanitize_text_field($status),
                'tracking_history' => maybe_serialize($tracking_history),
                'tracked_at' => current_time('mysql')
            ],
            ['%s', '%s', '%s', '%s']
        );
    }
    
    
    
    
}

// Initialize the plugin
WP_Shippo_Tracker::get_instance();
