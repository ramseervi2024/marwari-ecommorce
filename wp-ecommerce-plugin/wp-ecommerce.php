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

// Add custom direct URL rendering for website, admin, login, register, product, and dashboard
add_action('init', 'wp_ecommerce_custom_routes', 1);
add_action('parse_request', 'wp_ecommerce_custom_routes', 1);
add_action('template_redirect', 'wp_ecommerce_custom_routes', 1);
function wp_ecommerce_custom_routes($wp = null) {
    static $executed = false;
    if ($executed) return;

    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    
    // Ignore WordPress backend, REST API, or static assets
    if (strpos($request_uri, '/wp-admin') !== false || 
        strpos($request_uri, '/wp-json') !== false || 
        strpos($request_uri, '/wp-includes') !== false ||
        strpos($request_uri, '/wp-content') !== false) {
        return;
    }

    $plugin_url = plugin_dir_url(__FILE__);
    $cache_ver = time() . '115';

    // Route: Swagger API Documentation (Interactive Swagger UI)
    if (strpos($request_uri, '/ecommerce/api-docs') !== false || strpos($request_uri, '/ecommerce/swagger') !== false) {
        $executed = true;
        if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);
        nocache_headers();
        $html = file_get_contents( WP_ECOMMERCE_PLUGIN_DIR . 'swagger.html' );
        echo $html;
        exit;
    }

    // Route: Admin Super Panel
    if (strpos($request_uri, '/ecommerce/admin') !== false || strpos($request_uri, '/superpanel') !== false) {
        $executed = true;
        if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);
        nocache_headers();
        $html = file_get_contents( WP_ECOMMERCE_PLUGIN_DIR . 'superpanel.html' );
        $html = str_replace('href="style.css"', 'href="' . $plugin_url . 'style.css?v=' . $cache_ver . '"', $html);
        $html = str_replace('src="app.js"', 'src="' . $plugin_url . 'app.js?v=' . $cache_ver . '"', $html);
        $html = str_replace('src="customer-management.js"', 'src="' . $plugin_url . 'customer-management.js?v=' . $cache_ver . '"', $html);
        echo $html;
        exit;
    }

    // Routes: Frontend E-Commerce (/ecommerce/website, /ecommerce/login, /ecommerce/register, /ecommerce/product, /ecommerce/dashboard, /ecommerce/account)
    $matches_ecommerce = (
        strpos($request_uri, '/ecommerce') !== false || 
        strpos($request_uri, '/website') !== false ||
        strpos($request_uri, '/login') !== false || 
        strpos($request_uri, '/register') !== false || 
        strpos($request_uri, '/product') !== false ||
        strpos($request_uri, '/dashboard') !== false ||
        strpos($request_uri, '/account') !== false
    );

    if ($matches_ecommerce) {
        $executed = true;
        if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);
        nocache_headers();
        $html = file_get_contents( WP_ECOMMERCE_PLUGIN_DIR . 'index.html' );
        $html = str_replace('href="style.css"', 'href="' . $plugin_url . 'style.css?v=' . $cache_ver . '"', $html);
        $html = str_replace('src="app.js"', 'src="' . $plugin_url . 'app.js?v=' . $cache_ver . '"', $html);
        echo $html;
        exit;
    }
}

