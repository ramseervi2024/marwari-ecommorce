<?php
/**
 * Plugin Name: Gym & Fitness ERP API
 * Description: Complete Gym Management System — Memberships, Renewals, Trainers, Diet Plans, Attendance, and Payments. REST API with JWT auth and light-theme SPA dashboard.
 * Version: 1.0.0
 * Author: Ramesh Seervi
 * Text Domain: gym-erp-api
 */

if (!defined('ABSPATH')) {
    exit;
}

define('GYM_ERP_VERSION', '1.0.0');
define('GYM_ERP_DIR', __DIR__ . '/');

// 1. PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix  = 'GymErpApi\\';
    $base    = __DIR__ . '/';
    $len     = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $rel     = substr($class, $len);
    $parts   = explode('\\', $rel);
    $file    = array_pop($parts);
    $path    = strtolower(implode('/', $parts));
    $full    = $base . ($path ? $path . '/' : '') . $file . '.php';
    if (file_exists($full)) require_once $full;
});

// 2. Database migrations
require_once __DIR__ . '/database/Migrations.php';

// 3. Route files
$route_files = ['auth', 'dashboard', 'member', 'trainer', 'plan', 'membership', 'payment', 'diet_plan', 'attendance', 'workout_plan', 'equipment'];
foreach ($route_files as $r) {
    require_once __DIR__ . "/routes/{$r}.php";
}

// 4. Activation
register_activation_hook(__FILE__, 'gym_erp_activate');
function gym_erp_activate() {
    \GymErpApi\Database\Migrations::activate();
    add_rewrite_rule('^gym-management/?$', 'index.php?gym_erp=1', 'top');
    add_rewrite_rule('^gym-management-docs/?$', 'index.php?gym_erp_docs=1', 'top');
    flush_rewrite_rules();
}

// 5. Register REST routes
add_action('rest_api_init', function () {
    \GymErpApi\Routes\AuthRoutes::register();
    \GymErpApi\Routes\DashboardRoutes::register();
    \GymErpApi\Routes\MemberRoutes::register();
    \GymErpApi\Routes\TrainerRoutes::register();
    \GymErpApi\Routes\PlanRoutes::register();
    \GymErpApi\Routes\MembershipRoutes::register();
    \GymErpApi\Routes\PaymentRoutes::register();
    \GymErpApi\Routes\DietPlanRoutes::register();
    \GymErpApi\Routes\AttendanceRoutes::register();
    \GymErpApi\Routes\WorkoutPlanRoutes::register();
    \GymErpApi\Routes\EquipmentRoutes::register();
});

// 6. CORS
add_action('rest_api_init', function () {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function ($value) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Authorization, X-WP-Nonce, Content-Type, X-Requested-With');
        if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) { status_header(200); exit(); }
        return $value;
    });
}, 15);

// 7. Rewrite rules & query vars
add_action('init', function () {
    add_rewrite_rule('^gym-management/?$', 'index.php?gym_erp=1', 'top');
    add_rewrite_rule('^gym-management-docs/?$', 'index.php?gym_erp_docs=1', 'top');
});
add_filter('query_vars', function ($vars) {
    $vars[] = 'gym_erp';
    $vars[] = 'gym_erp_docs';
    return $vars;
});
add_action('template_redirect', function () {
    if (get_query_var('gym_erp')) {
        include __DIR__ . '/views/dashboard-view.php'; exit;
    }
    if (get_query_var('gym_erp_docs')) {
        include __DIR__ . '/swagger/index.php'; exit;
    }
});

// 8. Admin Menu Link
add_action('admin_menu', function () {
    add_menu_page(
        'Gym ERP',
        'Gym ERP',
        'read',
        'gym-management',
        function () {
            echo '<script>window.location.href="' . esc_url(site_url('/gym-management')) . '";</script>';
        },
        'dashicons-universal-access-alt',
        31
    );
});
