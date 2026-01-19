<?php

if (!defined('ABSPATH')) {
    exit;
}

function wp_shippo_tracking_shortcode($atts) {
    ob_start();
    ?>
    <div class="wp-shippo-tracker">
        <form id="tracking-form" class="tracking-form">
            <select name="carrier" required>
                <option value="">Select Carrier</option>
                <option value="usps">USPS</option>
                <option value="ups">UPS</option>
                <option value="fedex">FedEx</option>
                <option value="dhl">DHL</option>
            </select>
            
            <input type="text" name="tracking_number" placeholder="Enter tracking number" required>
            
            <button type="submit">Track Package</button>
        </form>
        
        <div id="tracking-results" class="tracking-results"></div>
    </div>
    <?php
    return ob_get_clean();
}

function register_shippo_shortcode() {
    add_shortcode('shippo_tracker', 'wp_shippo_tracking_shortcode'); 
}
add_action('init', 'register_shippo_shortcode'); 
