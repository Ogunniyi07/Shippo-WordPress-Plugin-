<?php

if (!defined('ABSPATH')) {
    exit;
}

class WP_Shippo_Tracker_Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_admin_menu()
    {
        // Main menu
        add_menu_page(
            'WP Shippo Tracker',
            'Shippo Tracker',
            'manage_options',
            'wp-shippo-tracker',
            [$this, 'tracking_page'],
            'dashicons-location',
            20
        );

        // Submenus
        add_submenu_page(
            'wp-shippo-tracker',
            'Shortcode',
            'Shortcode',
            'manage_options',
            'wp-shippo-tracker-shortcode',
            [$this, 'shortcode_page']
        );

        add_submenu_page(
            'wp-shippo-tracker',
            'Settings',
            'Settings',
            'manage_options',
            'wp-shippo-tracker-settings',
            [$this, 'render_settings_page']
        );
    }

    // Table to display the tracking search on the  website 
    public function tracking_page()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'shippo_tracking_records';
        $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY tracked_at DESC");

?>
        <div class="wrap">
            <h1>Tracking Records</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Tracking Number</th>
                        <th>Carrier</th>
                        <th>Status</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sn = 1;
                    ?>
                    <?php foreach ($results as $row) : ?>
                        <tr>
                            <td><?php echo $sn++; ?>.</td> 
                            <td><?php echo esc_html($row->tracking_number); ?></td>
                            <td><?php echo esc_html($row->carrier); ?></td> 
                            <td><?php echo esc_html($row->status); ?></td>
                            <td><?php echo esc_html($row->tracked_at); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php
    }



    // Shortcode page
    public function shortcode_page()
    {
    ?>
        <div class="wrap">
            <h1>Shippo Tracker Shortcode</h1>
            <p>Use the following shortcode to display the tracking form:</p>
            <code>[shippo_tracker]</code>
        </div>
    <?php
    }

    // Settings page (API settings)
    public function render_settings_page()
    {
    ?>
        <div class="wrap">
            <h1>Shippo Tracker Settings</h1>
            <form method="post" action="<?php echo esc_url(admin_url('options.php')); ?>">
                <?php
                settings_fields('wp_shippo_tracker_settings_group');
                do_settings_sections('wp-shippo-tracker-settings');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }

    public function register_settings()
    {
        register_setting('wp_shippo_tracker_settings_group', 'shippo_api_token');

        add_settings_section(
            'shippo_tracker_main',
            'API Settings',
            [$this, 'settings_section_callback'],
            'wp-shippo-tracker-settings'
        );

        add_settings_field(
            'shippo_api_token',
            'Shippo API Token',
            [$this, 'api_token_callback'],
            'wp-shippo-tracker-settings',
            'shippo_tracker_main'
        );
    }

    public function settings_section_callback()
    {
        echo '<p>Enter your Shippo API settings below:</p>';
    }

    public function api_token_callback()
    {
        $api_token = get_option('shippo_api_token');
        echo '<input type="text" id="shippo_api_token" name="shippo_api_token" value="' . esc_attr($api_token) . '" class="regular-text">';
    }
}

// Initialize the admin menu class
new WP_Shippo_Tracker_Admin();
