<?php
/**
 * Plugin Name: Customer Manager API
 * Description: Custom REST API for managing customers and providing statistics.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: customer-manager
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Require Composer autoloader if it exists
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Include migrations
require_once __DIR__ . '/database/migrations.php';

// Include route registration files
require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/routes/customer.php';
require_once __DIR__ . '/routes/dashboard.php';

// Plugin Activation Hook
register_activation_hook(__FILE__, 'customer_manager_activate_plugin');

function customer_manager_activate_plugin() {
    customer_manager_create_tables();
    customer_manager_create_roles();
}

// Add CORS support for API requests
add_action('rest_api_init', function () {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function ($value) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Expose-Headers: Link', false);
        header('Access-Control-Allow-Headers: Authorization, X-WP-Nonce, Content-Type, X-Requested-With');
        if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
            status_header(200);
            exit();
        }
        return $value;
    });
}, 15);

// Serve Swagger documentation
add_action('init', function() {
    add_rewrite_rule('^customer-api-docs/?$', 'index.php?customer_api_docs=1', 'top');
});

add_filter('query_vars', function($vars) {
    $vars[] = 'customer_api_docs';
    return $vars;
});

add_action('template_redirect', function() {
    if (get_query_var('customer_api_docs')) {
        include plugin_dir_path(__FILE__) . 'swagger/index.php';
        exit;
    }
});

// Create Swagger Rewrite rule on activation
register_activation_hook(__FILE__, function() {
    add_rewrite_rule('^customer-api-docs/?$', 'index.php?customer_api_docs=1', 'top');
    flush_rewrite_rules();
});
