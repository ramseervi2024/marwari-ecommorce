<?php
/**
 * Plugin Name: Wholesale Distribution ERP API
 * Description: Production-ready Wholesale Distribution ERP — Dealers, Orders, Pricing, Inventory, Dispatch, Credit Limits, Payments, Billing, Reports, Dealer Portal. REST API with JWT Auth and Swagger UI.
 * Version: 1.0.0
 * Author: Ramesh Seervi
 * Text Domain: wholesale-erp-api
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WHOLESALE_ERP_VERSION', '1.0.0');
define('WHOLESALE_ERP_DIR', __DIR__ . '/');

// 1. PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'WholesaleErp\\';
    $base   = __DIR__ . '/';
    $len    = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $rel    = substr($class, $len);
    $parts  = explode('\\', $rel);
    $file   = array_pop($parts);
    $path   = strtolower(implode('/', $parts));
    $full   = $base . ($path ? $path . '/' : '') . $file . '.php';
    if (file_exists($full)) require_once $full;
});

// 2. Database migrations
require_once __DIR__ . '/database/Migrations.php';

// 3. Route files
$route_files = [
    'auth', 'dashboard', 'dealer', 'product', 'pricing', 'order',
    'sales_rep', 'route', 'inventory', 'warehouse', 'dispatch',
    'credit_limit', 'payment', 'outstanding', 'purchase', 'supplier',
    'billing', 'reports', 'portal', 'media'
];
foreach ($route_files as $r) {
    require_once __DIR__ . "/routes/{$r}.php";
}

// 4. Activation hook
register_activation_hook(__FILE__, 'wholesale_erp_activate');
function wholesale_erp_activate() {
    \WholesaleErp\Database\Migrations::activate();
    add_rewrite_rule('^wholesale-management/?$', 'index.php?wholesale_erp=1', 'top');
    add_rewrite_rule('^wholesale-management-api-docs/?$', 'index.php?wholesale_erp_docs=1', 'top');
    flush_rewrite_rules();
}

// 5. Register REST routes
add_action('rest_api_init', function () {
    \WholesaleErp\Routes\AuthRoutes::register();
    \WholesaleErp\Routes\DashboardRoutes::register();
    \WholesaleErp\Routes\DealerRoutes::register();
    \WholesaleErp\Routes\ProductRoutes::register();
    \WholesaleErp\Routes\PricingRoutes::register();
    \WholesaleErp\Routes\OrderRoutes::register();
    \WholesaleErp\Routes\SalesRepRoutes::register();
    \WholesaleErp\Routes\RouteRoutes::register();
    \WholesaleErp\Routes\InventoryRoutes::register();
    \WholesaleErp\Routes\WarehouseRoutes::register();
    \WholesaleErp\Routes\DispatchRoutes::register();
    \WholesaleErp\Routes\CreditLimitRoutes::register();
    \WholesaleErp\Routes\PaymentRoutes::register();
    \WholesaleErp\Routes\OutstandingRoutes::register();
    \WholesaleErp\Routes\PurchaseRoutes::register();
    \WholesaleErp\Routes\SupplierRoutes::register();
    \WholesaleErp\Routes\BillingRoutes::register();
    \WholesaleErp\Routes\ReportsRoutes::register();
    \WholesaleErp\Routes\PortalRoutes::register();
    \WholesaleErp\Routes\MediaRoutes::register();
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
    add_rewrite_rule('^wholesale-management/?$', 'index.php?wholesale_erp=1', 'top');
    add_rewrite_rule('^wholesale-management-api-docs/?$', 'index.php?wholesale_erp_docs=1', 'top');
});
add_filter('query_vars', function ($vars) {
    $vars[] = 'wholesale_erp';
    $vars[] = 'wholesale_erp_docs';
    return $vars;
});
add_action('template_redirect', function () {
    if (get_query_var('wholesale_erp')) {
        include __DIR__ . '/views/dashboard-view.php'; exit;
    }
    if (get_query_var('wholesale_erp_docs')) {
        include __DIR__ . '/swagger/index.php'; exit;
    }
});

// 8. Admin Menu
add_action('admin_menu', function () {
    add_menu_page(
        'Wholesale ERP',
        'Wholesale ERP',
        'read',
        'wholesale-management',
        function () {
            echo '<script>window.location.href="' . esc_url(site_url('/wholesale-management')) . '";</script>';
        },
        'dashicons-store',
        30
    );
});
