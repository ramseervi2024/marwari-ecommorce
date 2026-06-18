<?php
/**
 * Plugin Name: Pharmacy ERP API
 * Description: Complete Pharmacy Management System — Medicine Stock, Batch Tracking, Expiry Alerts, Billing with GST, Purchase Management, Supplier Management. REST API with JWT auth and light-theme SPA dashboard.
 * Version: 1.0.0
 * Author: Ramesh Seervi
 * Text Domain: pharmacy-erp-api
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PHARMACY_ERP_VERSION', '1.0.0');
define('PHARMACY_ERP_DIR', __DIR__ . '/');

// 1. PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix  = 'PharmacyErpApi\\';
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
$route_files = ['auth', 'dashboard', 'medicine', 'batch', 'supplier', 'purchase', 'bill', 'stock'];
foreach ($route_files as $r) {
    require_once __DIR__ . "/routes/{$r}.php";
}

// 4. Activation
register_activation_hook(__FILE__, 'pharmacy_erp_activate');
function pharmacy_erp_activate() {
    \PharmacyErpApi\Database\Migrations::activate();
    add_rewrite_rule('^pharmacy-erp/?$', 'index.php?pharmacy_erp=1', 'top');
    add_rewrite_rule('^pharmacy-erp-docs/?$', 'index.php?pharmacy_erp_docs=1', 'top');
    flush_rewrite_rules();
}

// 5. Register REST routes
add_action('rest_api_init', function () {
    \PharmacyErpApi\Routes\AuthRoutes::register();
    \PharmacyErpApi\Routes\DashboardRoutes::register();
    \PharmacyErpApi\Routes\MedicineRoutes::register();
    \PharmacyErpApi\Routes\BatchRoutes::register();
    \PharmacyErpApi\Routes\SupplierRoutes::register();
    \PharmacyErpApi\Routes\PurchaseRoutes::register();
    \PharmacyErpApi\Routes\BillRoutes::register();
    \PharmacyErpApi\Routes\StockRoutes::register();
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
    add_rewrite_rule('^pharmacy-erp/?$', 'index.php?pharmacy_erp=1', 'top');
    add_rewrite_rule('^pharmacy-erp-docs/?$', 'index.php?pharmacy_erp_docs=1', 'top');
});
add_filter('query_vars', function ($vars) {
    $vars[] = 'pharmacy_erp';
    $vars[] = 'pharmacy_erp_docs';
    return $vars;
});
add_action('template_redirect', function () {
    if (get_query_var('pharmacy_erp')) {
        include __DIR__ . '/views/dashboard-view.php'; exit;
    }
    if (get_query_var('pharmacy_erp_docs')) {
        include __DIR__ . '/swagger/index.php'; exit;
    }
});

// 8. SMTP
add_filter('wp_mail_from', function ($e) {
    $from = get_option('pharmacy_smtp_from_email');
    if (empty($from)) return 'noreply@' . wp_parse_url(get_site_url(), PHP_URL_HOST);
    return $from;
});
add_filter('wp_mail_from_name', function ($n) { return get_option('pharmacy_smtp_from_name', 'Pharmacy ERP'); });
add_action('phpmailer_init', function ($m) {
    if (get_option('pharmacy_smtp_enabled') !== 'yes') return;
    $m->isSMTP();
    $m->Host     = get_option('pharmacy_smtp_host');
    $m->SMTPAuth = true;
    $m->Port     = get_option('pharmacy_smtp_port', '587');
    $m->Username = get_option('pharmacy_smtp_username');
    $m->Password = get_option('pharmacy_smtp_password');
    $enc = get_option('pharmacy_smtp_encryption', 'tls');
    $m->SMTPSecure = ($enc === 'none') ? '' : $enc;
});
