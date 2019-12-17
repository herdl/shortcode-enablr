<?php

/**
 * Plugin Name: Shablr
 * Plugin URI: https://github.com/herdl/shablr
 * Description: Short Code enabler.
 * Author: Herdl
 * Version: 1.0.0
 * Author URI: https://herdl.com
 */

if (!defined('WPINC')) {
    die('No direct access allowed');
}

function shablr_register_settings() {
    add_submenu_page('options-general.php', 'Shablr', 'Shablr', 'manage_options', 'shablr', 'shablr_settings');
}

function shablr_settings() {
    if (!current_user_can('administrator')) {
        echo '<p>Sorry, you are not allowed to access this page.</p>';
        return;
    }

    if (isset($_REQUEST['submit'])) {
        if (!isset($_REQUEST['shablr_nonce'])) {
            $errorMessage = 'nonce field is missing. Settings NOT saved.';
        } elseif (!wp_verify_nonce($_REQUEST['shablr_nonce'], 'shablr')) {
            $errorMessage = 'Invalid nonce specified. Settings NOT saved.';
        } else {
            update_option('shablr_acf_enable', isset($_REQUEST['shablr_acf_enable']) ? 'yes' : 'no');
            update_option('shablr_yoast_title_enable', isset($_REQUEST['shablr_yoast_title_enable']) ? 'yes' : 'no');

            $message = 'Settings Saved.';
        }
    }

    include_once(__DIR__ . '/templates/settings.php');
}

if (get_option('shablr_acf_enable') === 'yes') {
    add_filter('acf/format_value', 'shablr_acf_format_value');

    function shablr_acf_format_value($value, $post_id, $field) {
        if (!is_array($value)) {
            $value = do_shortcode($value);
        }

        return $value;
    }
}

if (get_option('shablr_yoast_title_enable') === 'yes') {
    add_filter('wpseo_title', 'shablr_wpseo_title');

    function shablr_wpseo_title($title) {
        return do_shortcode($title);
    }
}

add_action('admin_menu', 'shablr_register_settings');
add_action('wp_footer', 'shablr_render_script');
