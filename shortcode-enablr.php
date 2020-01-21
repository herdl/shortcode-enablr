<?php

/**
 * Plugin Name: Shortcode Enablr
 * Plugin URI: https://github.com/herdl/shortcode-enablr
 * Description: Shortcode enabler.
 * Author: Herdl
 * Version: 1.1.3
 * Author URI: https://herdl.com
 */

if (!defined('WPINC')) {
    die('No direct access allowed');
}

function shortcode_enablr_register_settings() {
    add_submenu_page('options-general.php', 'Shortcode Enablr', 'Shortcode Enablr', 'manage_options', 'shortcode_enablr', 'shortcode_enablr_settings');
}

function shortcode_enablr_settings() {
    if (!current_user_can('administrator')) {
        echo '<p>Sorry, you are not allowed to access this page.</p>';
        return;
    }

    if (isset($_REQUEST['submit'])) {
        if (!isset($_REQUEST['shortcode_enablr_nonce'])) {
            $errorMessage = 'nonce field is missing. Settings NOT saved.';
        } elseif (!wp_verify_nonce($_REQUEST['shortcode_enablr_nonce'], 'shortcode_enablr')) {
            $errorMessage = 'Invalid nonce specified. Settings NOT saved.';
        } else {
            update_option('shortcode_enablr_acf_enable', isset($_REQUEST['shortcode_enablr_acf_enable']) ? 'yes' : 'no');
            update_option('shortcode_enablr_yoast_title_enable', isset($_REQUEST['shortcode_enablr_yoast_title_enable']) ? 'yes' : 'no');

            $message = 'Settings Saved.';
        }
    }

    include_once(__DIR__ . '/templates/settings.php');
}

if (get_option('shortcode_enablr_acf_enable') === 'yes') {
    if (!class_exists('ACF')) {
        add_filter('acf/format_value', 'shortcode_enablr_acf_format_value');

        function shortcode_enablr_acf_format_value($value, $post_id, $field) {
            if (!is_array($value)) {
                $value = do_shortcode($value);
            }

            return $value;
        }
    }
}

if (get_option('shortcode_enablr_yoast_title_enable') === 'yes') {
    add_filter('wpseo_title', 'shortcode_enablr_wpseo_title');

    function shortcode_enablr_wpseo_title($title)
    {
        return do_shortcode($title);
    }
}

add_action('admin_menu', 'shortcode_enablr_register_settings');
