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

// 1. Simple PSR-4 Autoloader (Replaces Composer Autoloader for ZIP deployment)
spl_autoload_register(function ($class) {
    $prefix = 'CustomerManager\\';
    $base_dir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    
    $parts = explode('\\', $relative_class);
    $fileName = array_pop($parts);
    // Lowercase the directory paths to match our folder structure (models, controllers, etc.)
    $path = strtolower(implode('/', $parts)); 
    
    $file = $base_dir . ($path ? $path . '/' : '') . $fileName . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// 2. Include migrations
require_once __DIR__ . '/database/migrations.php';

// 3. Include route files
require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/routes/customer.php';
require_once __DIR__ . '/routes/dashboard.php';

// 4. Plugin Activation Hook
register_activation_hook(__FILE__, 'customer_manager_activate_plugin');

function customer_manager_activate_plugin() {
    \CustomerManager\Database\Migrations::activate();
    add_rewrite_rule('^customer-api-docs/?$', 'index.php?customer_api_docs=1', 'top');
    flush_rewrite_rules();
}

// 5. Register REST routes
add_action('rest_api_init', function () {
    \CustomerManager\Routes\AuthRoutes::register();
    \CustomerManager\Routes\CustomerRoutes::register();
    \CustomerManager\Routes\DashboardRoutes::register();
});

// 6. Add CORS support
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

// 7. Serve Swagger documentation
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
