<?php
/**
 * Plugin Name: Jewellery ERP API
 * Description: Custom REST API Jewellery Management System. Gold/Silver bullion stock tracking, finished ornaments barcode scan, Karigar job details, buyback exchange rate calculator, billing invoices, repairs, and diagnostics.
 * Version: 1.0.0
 * Author: Ramesh Seervi
 * Text Domain: jewellery-management-api
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('JEWELLERY_MANAGEMENT_VERSION', '1.0.0');

// 1. PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'JewelleryManagementApi\\';
    $base_dir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    
    $parts = explode('\\', $relative_class);
    $fileName = array_pop($parts);
    $path = strtolower(implode('/', $parts)); 
    
    $file = $base_dir . ($path ? $path . '/' : '') . $fileName . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// 2. Include database migrations
require_once __DIR__ . '/database/Migrations.php';

// 3. Include route files
$route_files = [
    'auth', 'inventory', 'metal_stock', 'customer', 'billing', 'karigar', 'job_work', 'repair', 
    'custom_order', 'buyback', 'diamond', 'expense', 'loyalty', 'inventory_audit', 'barcode',
    'media', 'notification', 'dashboard', 'reports'
];
foreach ($route_files as $route) {
    require_once __DIR__ . "/routes/{$route}.php";
}

// 4. Plugin Activation Hook
register_activation_hook(__FILE__, 'jewellery_management_activate_plugin');

function jewellery_management_activate_plugin() {
    \JewelleryManagementApi\Database\Migrations::activate();
    add_rewrite_rule('^jewellery-management-api-docs/?$', 'index.php?jewellery_management_api_docs=1', 'top');
    add_rewrite_rule('^jewellery-management/?$', 'index.php?jewellery_management=1', 'top');
    flush_rewrite_rules();
}

// 5. Register REST routes
add_action('rest_api_init', function () {
    \JewelleryManagementApi\Routes\AuthRoutes::register();
    \JewelleryManagementApi\Routes\InventoryRoutes::register();
    \JewelleryManagementApi\Routes\MetalStockRoutes::register();
    \JewelleryManagementApi\Routes\CustomerRoutes::register();
    \JewelleryManagementApi\Routes\BillingRoutes::register();
    \JewelleryManagementApi\Routes\KarigarRoutes::register();
    \JewelleryManagementApi\Routes\JobWorkRoutes::register();
    \JewelleryManagementApi\Routes\RepairRoutes::register();
    \JewelleryManagementApi\Routes\CustomOrderRoutes::register();
    \JewelleryManagementApi\Routes\BuybackRoutes::register();
    \JewelleryManagementApi\Routes\DiamondRoutes::register();
    \JewelleryManagementApi\Routes\ExpenseRoutes::register();
    \JewelleryManagementApi\Routes\LoyaltyRoutes::register();
    \JewelleryManagementApi\Routes\InventoryAuditRoutes::register();
    \JewelleryManagementApi\Routes\BarcodeRoutes::register();
    \JewelleryManagementApi\Routes\MediaRoutes::register();
    \JewelleryManagementApi\Routes\NotificationRoutes::register();
    \JewelleryManagementApi\Routes\DashboardRoutes::register();
    \JewelleryManagementApi\Routes\ReportsRoutes::register();
});

// 6. CORS Configuration
add_action('rest_api_init', function () {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function ($value) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Expose-Headers: Link', false);
        header('Access-Control-Allow-Headers: Authorization, X-WP-Nonce, Content-Type, X-Requested-With, X-Authorization');
        if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
            status_header(200);
            exit();
        }
        return $value;
    });
}, 15);

// 7. Serve Swagger documentation & Custom View
add_action('init', function() {
    add_rewrite_rule('^jewellery-management-api-docs/?$', 'index.php?jewellery_management_api_docs=1', 'top');
    add_rewrite_rule('^jewellery-management/?$', 'index.php?jewellery_management=1', 'top');
    
    // Self-correct invalid email template settings
    $template = get_option('jwl_email_template');
    if (!$template || strpos($template, '{otp}') === false) {
        update_option('jwl_email_template', "Hello {name},\n\nYour 6-digit verification code is: {otp}\n\nThis code is valid for 15 minutes.\n\nThank you!");
    }
});

add_filter('query_vars', function($vars) {
    $vars[] = 'jewellery_management_api_docs';
    $vars[] = 'jewellery_management';
    return $vars;
});

add_action('template_redirect', function() {
    if (get_query_var('jewellery_management_api_docs')) {
        include plugin_dir_path(__FILE__) . 'swagger/index.php';
        exit;
    }
    if (get_query_var('jewellery_management')) {
        include plugin_dir_path(__FILE__) . 'views/dashboard-view.php';
        exit;
    }
});

// 8. SMTP and Mail configuration setup
add_filter('wp_mail_from', 'jewellery_management_mail_from');
function jewellery_management_mail_from($original_email_address) {
    $from_email = get_option('jwl_smtp_from_email');
    if (empty($from_email) || strpos($from_email, 'gmail.com') !== false) {
        $domain = wp_parse_url(get_site_url(), PHP_URL_HOST);
        return 'noreply@' . $domain;
    }
    return $from_email;
}

add_filter('wp_mail_from_name', 'jewellery_management_mail_from_name');
function jewellery_management_mail_from_name($original_email_from_name) {
    return get_option('jwl_smtp_from_name', 'Jewellery ERP');
}

add_action('phpmailer_init', 'jewellery_management_phpmailer_init');
function jewellery_management_phpmailer_init($phpmailer) {
    $smtp_enabled = get_option('jwl_smtp_enabled', 'no');
    if ($smtp_enabled !== 'yes') {
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host       = get_option('jwl_smtp_host');
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = get_option('jwl_smtp_port', '587');
    $phpmailer->Username   = get_option('jwl_smtp_username');
    $phpmailer->Password   = get_option('jwl_smtp_password');
    
    $encryption = get_option('jwl_smtp_encryption', 'tls');
    if ($encryption === 'ssl') {
        $phpmailer->SMTPSecure = 'ssl';
    } elseif ($encryption === 'tls') {
        $phpmailer->SMTPSecure = 'tls';
    } else {
        $phpmailer->SMTPSecure = '';
    }

    $from_email = get_option('jwl_smtp_from_email');
    if (!empty($from_email)) {
        $phpmailer->From = $from_email;
    }
    $from_name = get_option('jwl_smtp_from_name');
    if (!empty($from_name)) {
        $phpmailer->FromName = $from_name;
    }
}

// 9. Log wp_mail errors to activity log and public diagnostics file
add_action('wp_mail_failed', function($error) {
    if (is_wp_error($error)) {
        \JewelleryManagementApi\Services\AuthService::logActivity(null, 'MAIL_FAILED', $error->get_error_message());
        
        $log_file = __DIR__ . '/mail_log.txt';
        $to_addresses = '';
        $error_data = $error->get_error_data();
        if (isset($error_data['to'])) {
            $to_addresses = implode(', ', (array)$error_data['to']);
        }
        $log_entry = date('[Y-m-d H:i:s] ') . 'Mail failed to: [' . $to_addresses . '] | Error: ' . $error->get_error_message() . "\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
});
