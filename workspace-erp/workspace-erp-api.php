<?php
/**
 * Plugin Name: Workspace ERP API
 * Description: Aurbis Workspace Management ERP - Complete REST API backend for managed office spaces, coworking, enterprise workspaces, facility operations, billing, sustainability, and mobile app integration.
 * Version: 1.0.0
 * Author: Ramesh Seervi
 * Text Domain: workspace-erp-api
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WORKSPACE_ERP_VERSION', '1.0.0');
define('WORKSPACE_ERP_DIR', __DIR__);
define('WORKSPACE_ERP_URL', plugin_dir_url(__FILE__));

// 1. PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'WorkspaceErpApi\\';
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
    'auth', 'dashboard', 'crm', 'client', 'workspace', 'occupancy',
    'visitor', 'facility', 'asset', 'vendor', 'billing', 'sustainability',
    'smartbuilding', 'hr', 'community', 'mobile', 'reports', 'notification', 'analytics'
];
foreach ($route_files as $route) {
    require_once __DIR__ . "/routes/{$route}.php";
}

// 4. Plugin Activation Hook
register_activation_hook(__FILE__, 'workspace_erp_activate_plugin');

function workspace_erp_activate_plugin() {
    \WorkspaceErpApi\Database\Migrations::activate();
    add_rewrite_rule('^workspace-erp-docs/?$', 'index.php?workspace_erp_docs=1', 'top');
    add_rewrite_rule('^workspace-erp/?$', 'index.php?workspace_erp=1', 'top');
    flush_rewrite_rules();
}

// 5. Plugin Deactivation Hook
register_deactivation_hook(__FILE__, 'workspace_erp_deactivate_plugin');

function workspace_erp_deactivate_plugin() {
    flush_rewrite_rules();
}

// 6. Register REST routes
add_action('rest_api_init', function () {
    \WorkspaceErpApi\Routes\AuthRoutes::register();
    \WorkspaceErpApi\Routes\DashboardRoutes::register();
    \WorkspaceErpApi\Routes\CrmRoutes::register();
    \WorkspaceErpApi\Routes\ClientRoutes::register();
    \WorkspaceErpApi\Routes\WorkspaceRoutes::register();
    \WorkspaceErpApi\Routes\OccupancyRoutes::register();
    \WorkspaceErpApi\Routes\VisitorRoutes::register();
    \WorkspaceErpApi\Routes\FacilityRoutes::register();
    \WorkspaceErpApi\Routes\AssetRoutes::register();
    \WorkspaceErpApi\Routes\VendorRoutes::register();
    \WorkspaceErpApi\Routes\BillingRoutes::register();
    \WorkspaceErpApi\Routes\SustainabilityRoutes::register();
    \WorkspaceErpApi\Routes\SmartBuildingRoutes::register();
    \WorkspaceErpApi\Routes\HrRoutes::register();
    \WorkspaceErpApi\Routes\CommunityRoutes::register();
    \WorkspaceErpApi\Routes\MobileRoutes::register();
    \WorkspaceErpApi\Routes\ReportsRoutes::register();
    \WorkspaceErpApi\Routes\NotificationRoutes::register();
    \WorkspaceErpApi\Routes\AnalyticsRoutes::register();
});

// 7. CORS Configuration
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

// 8. Rewrite rules + query vars
add_action('init', function () {
    add_rewrite_rule('^workspace-erp-docs/?$', 'index.php?workspace_erp_docs=1', 'top');
    add_rewrite_rule('^workspace-erp/?$', 'index.php?workspace_erp=1', 'top');

    $template = get_option('workspace_email_template');
    if (!$template || strpos($template, '{otp}') === false) {
        update_option('workspace_email_template', "Hello {name},\n\nYour 6-digit verification code is: {otp}\n\nThis code is valid for 15 minutes.\n\nThank you,\nAurbis Workspace ERP Team");
    }
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'workspace_erp_docs';
    $vars[] = 'workspace_erp';
    return $vars;
});

add_action('template_redirect', function () {
    if (get_query_var('workspace_erp_docs')) {
        include plugin_dir_path(__FILE__) . 'swagger/index.php';
        exit;
    }
    if (get_query_var('workspace_erp')) {
        include plugin_dir_path(__FILE__) . 'views/dashboard-view.php';
        exit;
    }
});

// 9. SMTP Mail configuration
add_filter('wp_mail_from', 'workspace_erp_mail_from');
function workspace_erp_mail_from($original_email_address) {
    $from_email = get_option('workspace_smtp_from_email');
    if (empty($from_email) || strpos($from_email, 'gmail.com') !== false) {
        $domain = wp_parse_url(get_site_url(), PHP_URL_HOST);
        return 'noreply@' . $domain;
    }
    return $from_email;
}

add_filter('wp_mail_from_name', 'workspace_erp_mail_from_name');
function workspace_erp_mail_from_name($original_email_from_name) {
    return get_option('workspace_smtp_from_name', 'Aurbis Workspace ERP');
}

add_action('phpmailer_init', 'workspace_erp_phpmailer_init');
function workspace_erp_phpmailer_init($phpmailer) {
    $smtp_enabled = get_option('workspace_smtp_enabled', 'no');
    if ($smtp_enabled !== 'yes') return;

    $phpmailer->isSMTP();
    $phpmailer->Host     = get_option('workspace_smtp_host');
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port     = get_option('workspace_smtp_port', '587');
    $phpmailer->Username = get_option('workspace_smtp_username');
    $phpmailer->Password = get_option('workspace_smtp_password');

    $encryption = get_option('workspace_smtp_encryption', 'tls');
    $phpmailer->SMTPSecure = ($encryption === 'ssl') ? 'ssl' : (($encryption === 'tls') ? 'tls' : '');

    $from_email = get_option('workspace_smtp_from_email');
    if (!empty($from_email)) $phpmailer->From = $from_email;
    $from_name = get_option('workspace_smtp_from_name');
    if (!empty($from_name)) $phpmailer->FromName = $from_name;
}

// 10. Log mail failures
add_action('wp_mail_failed', function ($error) {
    if (is_wp_error($error)) {
        \WorkspaceErpApi\Services\AuthService::logActivity(null, 'MAIL_FAILED', $error->get_error_message());
        $log_file = __DIR__ . '/mail_log.txt';
        $error_data = $error->get_error_data();
        $to_addresses = isset($error_data['to']) ? implode(', ', (array)$error_data['to']) : '';
        $log_entry = date('[Y-m-d H:i:s] ') . 'Mail failed to: [' . $to_addresses . '] | Error: ' . $error->get_error_message() . "\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
});
