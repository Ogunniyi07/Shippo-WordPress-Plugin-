<?php
if (!defined('ABSPATH')) {
    exit;
}

class WP_Shippo_Tracker_Ajax {
    public function __construct() {
        add_action('wp_ajax_wp_shippo_track_shipment', [$this, 'ajax_track_shipment']);
        add_action('wp_ajax_nopriv_wp_shippo_track_shipment', [$this, 'ajax_track_shipment']);
    }

    public function ajax_track_shipment() {
        check_ajax_referer('wp-shippo-tracker-nonce', 'nonce');
    
        if (!isset($_POST['carrier']) || !isset($_POST['tracking_number'])) {
            wp_send_json_error('Carrier and tracking number are required.');
            return;
        }
    
        $carrier = sanitize_text_field($_POST['carrier']);
        $tracking_number = sanitize_text_field($_POST['tracking_number']);
        $api_token = get_option('shippo_api_token');
    
        if (empty($api_token)) {
            wp_send_json_error('Shippo API token is not configured.');
            return;
        }
    
        $response = wp_remote_post('https://api.goshippo.com/tracks/', array(
            'headers' => array(
                'Authorization' => 'ShippoToken ' . $api_token,
                'Content-Type'  => 'application/json',
            ),
            'body' => json_encode(array(
                'carrier' => strtolower($carrier),
                'tracking_number' => $tracking_number,
            )),
        ));
    
        if (is_wp_error($response)) {
            wp_send_json_error('API request failed: ' . $response->get_error_message());
            return;
        }
    
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
    
        if (!isset($data['tracking_status']) || !isset($data['tracking_history'])) {
            wp_send_json_error('Invalid response from Shippo API');
            return;
        }
    
        // Extract relevant data
        $status = $data['tracking_status']['status'];
        $history = maybe_serialize($data['tracking_history']);
    
        // ✅ Get the main class instance and call `log_tracking_data()`
        if (class_exists('WP_Shippo_Tracker')) {
            $shippo_tracker = WP_Shippo_Tracker::get_instance();
            $shippo_tracker->log_tracking_data($tracking_number, $carrier, $status, $history);
        } else {
            error_log('❌ WP_Shippo_Tracker class not found.');
        }
    
        // Return the tracking data in AJAX response
        wp_send_json_success($data);
    }
}

// Initialize the AJAX handler
new WP_Shippo_Tracker_Ajax();
