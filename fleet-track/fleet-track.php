<?php
/**
 * Plugin Name: FleetTrack Pro API
 * Description: Custom REST API for Fleet Management (vehicles, drivers, routes, trips, expenses, fuel, documents, dashboard, reports).
 * Version: 1.0.0
 * Author: Ramesh Seervi
 * Text Domain: fleet-track
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('FLEET_TRACK_VERSION', '1.0.0');

// 1. PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'FleetTrackPro\\';
    $base_dir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    
    $parts = explode('\\', $relative_class);
    $fileName = array_pop($parts);
    // Lowercase the directory paths to match folder structure (models, controllers, etc.)
    $path = strtolower(implode('/', $parts)); 
    
    $file = $base_dir . ($path ? $path . '/' : '') . $fileName . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// 2. Include database migrations
require_once __DIR__ . '/database/Migrations.php';

// 3. Include route files
require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/routes/vehicle.php';
require_once __DIR__ . '/routes/driver.php';
require_once __DIR__ . '/routes/route.php';
require_once __DIR__ . '/routes/trip.php';
require_once __DIR__ . '/routes/expense.php';
require_once __DIR__ . '/routes/fuel.php';
require_once __DIR__ . '/routes/media.php';
require_once __DIR__ . '/routes/dashboard.php';
require_once __DIR__ . '/routes/reports.php';

// 4. Plugin Activation Hook
register_activation_hook(__FILE__, 'fleet_track_activate_plugin');

function fleet_track_activate_plugin() {
    \FleetTrackPro\Database\Migrations::activate();
    add_rewrite_rule('^fleettrack-api-docs/?$', 'index.php?fleettrack_api_docs=1', 'top');
    add_rewrite_rule('^fleet-track/?$', 'index.php?fleet_track=1', 'top');
    flush_rewrite_rules();
}

// 5. Register REST routes
add_action('rest_api_init', function () {
    \FleetTrackPro\Routes\AuthRoutes::register();
    \FleetTrackPro\Routes\VehicleRoutes::register();
    \FleetTrackPro\Routes\DriverRoutes::register();
    \FleetTrackPro\Routes\RouteRoutes::register();
    \FleetTrackPro\Routes\TripRoutes::register();
    \FleetTrackPro\Routes\ExpenseRoutes::register();
    \FleetTrackPro\Routes\FuelRoutes::register();
    \FleetTrackPro\Routes\MediaRoutes::register();
    \FleetTrackPro\Routes\DashboardRoutes::register();
    \FleetTrackPro\Routes\ReportRoutes::register();
});

// 6. CORS Configuration
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
    add_rewrite_rule('^fleettrack-api-docs/?$', 'index.php?fleettrack_api_docs=1', 'top');
    add_rewrite_rule('^fleet-track/?$', 'index.php?fleet_track=1', 'top');
});

add_filter('query_vars', function($vars) {
    $vars[] = 'fleettrack_api_docs';
    $vars[] = 'fleet_track';
    return $vars;
});

add_action('template_redirect', function() {
    if (get_query_var('fleettrack_api_docs')) {
        include plugin_dir_path(__FILE__) . 'swagger/index.php';
        exit;
    }
    if (get_query_var('fleet_track')) {
        include plugin_dir_path(__FILE__) . 'views/fleet-dashboard.php';
        exit;
    }
});
