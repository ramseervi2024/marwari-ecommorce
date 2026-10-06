<?php
/**
 * Plugin Name:       WP E-Commerce Plugin
 * Description:       A comprehensive E-Commerce plugin with Admin Panel, Website, and Backend APIs.
 * Version:           1.0.0
 * Author:            Antigravity
 * Text Domain:       wp-ecommerce
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define constants
define( 'WP_ECOMMERCE_VERSION', '1.0.0' );
define( 'WP_ECOMMERCE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_wp_ecommerce() {
    // Add activation logic here (e.g., creating custom database tables)
}
register_activation_hook( __FILE__, 'activate_wp_ecommerce' );

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wp_ecommerce() {
    // Add deactivation logic here
}
register_deactivation_hook( __FILE__, 'deactivate_wp_ecommerce' );

// Include required core files
require_once WP_ECOMMERCE_PLUGIN_DIR . 'admin/class-wp-ecommerce-admin.php';
require_once WP_ECOMMERCE_PLUGIN_DIR . 'public/class-wp-ecommerce-public.php';
require_once WP_ECOMMERCE_PLUGIN_DIR . 'includes/api/class-wp-ecommerce-api.php';

/**
 * Begins execution of the plugin.
 */
function run_wp_ecommerce() {
    // Initialize Admin Panel
    $plugin_admin = new WP_ECommerce_Admin();
    $plugin_admin->init();

    // Initialize Public Website
    $plugin_public = new WP_ECommerce_Public();
    $plugin_public->init();

    // Initialize Backend APIs
    $plugin_api = new WP_ECommerce_API();
    $plugin_api->init();
}
run_wp_ecommerce();

// Add custom direct URL rendering for website and admin
add_action('parse_request', 'wp_ecommerce_custom_routes');
function wp_ecommerce_custom_routes($wp) {
    $request_uri = $_SERVER['REQUEST_URI'];
    $plugin_url = plugin_dir_url(__FILE__);
    
    if (strpos($request_uri, '/ecommerce/website') !== false) {
        $html = file_get_contents( WP_ECOMMERCE_PLUGIN_DIR . 'index.html' );
        $html = str_replace('href="style.css"', 'href="' . $plugin_url . 'style.css?v=' . time() . '5"', $html);
        $html = str_replace('src="app.js"', 'src="' . $plugin_url . 'app.js?v=' . time() . '5"', $html);
        echo $html;
        exit;
    }
    
    if (strpos($request_uri, '/ecommerce/admin') !== false) {
        $html = file_get_contents( WP_ECOMMERCE_PLUGIN_DIR . 'superpanel.html' );
        $html = str_replace('href="style.css"', 'href="' . $plugin_url . 'style.css?v=' . time() . '4"', $html);
        $html = str_replace('src="app.js"', 'src="' . $plugin_url . 'app.js?v=' . time() . '4"', $html);
        $html = str_replace('src="customer-management.js"', 'src="' . $plugin_url . 'customer-management.js?v=' . time() . '4"', $html);
        echo $html;
        exit;
    }
}

