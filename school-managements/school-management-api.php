<?php
/**
 * Plugin Name: School Management API
 * Description: Custom REST API ERP and School Management System for schools, colleges, and coaching centers. Includes database migrations, JWT auth, custom user roles, and Swagger OpenAPI docs.
 * Version: 1.0.0
 * Author: Ramesh Seervi
 * Text Domain: school-management-api
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('SCHOOL_MANAGEMENT_VERSION', '1.0.0');

// 1. PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'SchoolManagementApi\\';
    $base_dir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    
    $parts = explode('\\', $relative_class);
    $fileName = array_pop($parts);
    // Lowercase the directory paths to match folder structure
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
    'auth', 'student', 'teacher', 'parent', 'academic', 'attendance', 'timetable', 
    'homework', 'exam', 'fee', 'payroll', 'library', 'transport', 'hostel', 
    'event', 'notification', 'media', 'analytics', 'dashboard'
];
foreach ($route_files as $route) {
    require_once __DIR__ . "/routes/{$route}.php";
}

// 4. Plugin Activation Hook
register_activation_hook(__FILE__, 'school_management_activate_plugin');

function school_management_activate_plugin() {
    \SchoolManagementApi\Database\Migrations::activate();
    add_rewrite_rule('^school-management-api-docs/?$', 'index.php?school_management_api_docs=1', 'top');
    add_rewrite_rule('^school-management/?$', 'index.php?school_management=1', 'top');
    flush_rewrite_rules();
}

// 5. Register REST routes
add_action('rest_api_init', function () {
    \SchoolManagementApi\Routes\AuthRoutes::register();
    \SchoolManagementApi\Routes\StudentRoutes::register();
    \SchoolManagementApi\Routes\TeacherRoutes::register();
    \SchoolManagementApi\Routes\ParentRoutes::register();
    \SchoolManagementApi\Routes\AcademicRoutes::register();
    \SchoolManagementApi\Routes\AttendanceRoutes::register();
    \SchoolManagementApi\Routes\TimetableRoutes::register();
    \SchoolManagementApi\Routes\HomeworkRoutes::register();
    \SchoolManagementApi\Routes\ExamRoutes::register();
    \SchoolManagementApi\Routes\FeeRoutes::register();
    \SchoolManagementApi\Routes\PayrollRoutes::register();
    \SchoolManagementApi\Routes\LibraryRoutes::register();
    \SchoolManagementApi\Routes\TransportRoutes::register();
    \SchoolManagementApi\Routes\HostelRoutes::register();
    \SchoolManagementApi\Routes\EventRoutes::register();
    \SchoolManagementApi\Routes\NotificationRoutes::register();
    \SchoolManagementApi\Routes\MediaRoutes::register();
    \SchoolManagementApi\Routes\AnalyticsRoutes::register();
    \SchoolManagementApi\Routes\DashboardRoutes::register();
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

// 7. Serve Swagger documentation & Custom View
add_action('init', function() {
    add_rewrite_rule('^school-management-api-docs/?$', 'index.php?school_management_api_docs=1', 'top');
    add_rewrite_rule('^school-management/?$', 'index.php?school_management=1', 'top');
});

add_filter('query_vars', function($vars) {
    $vars[] = 'school_management_api_docs';
    $vars[] = 'school_management';
    return $vars;
});

add_action('template_redirect', function() {
    if (get_query_var('school_management_api_docs')) {
        include plugin_dir_path(__FILE__) . 'swagger/index.php';
        exit;
    }
    if (get_query_var('school_management')) {
        include plugin_dir_path(__FILE__) . 'views/dashboard-view.php';
        exit;
    }
});

// 8. SMTP and Mail configuration setup
add_filter('wp_mail_from', 'school_management_mail_from');
function school_management_mail_from($original_email_address) {
    $from_email = get_option('school_smtp_from_email');
    
    // If From email is empty or uses a third-party Gmail address (which causes SPF/DMARC failure on default mail),
    // default to a domain-aligned email to ensure reliability.
    if (empty($from_email) || strpos($from_email, 'gmail.com') !== false) {
        $domain = wp_parse_url(get_site_url(), PHP_URL_HOST);
        return 'noreply@' . $domain;
    }
    return $from_email;
}

add_filter('wp_mail_from_name', 'school_management_mail_from_name');
function school_management_mail_from_name($original_email_from_name) {
    return get_option('school_smtp_from_name', 'Global School ERP');
}

add_action('phpmailer_init', 'school_management_phpmailer_init');
function school_management_phpmailer_init($phpmailer) {
    $smtp_enabled = get_option('school_smtp_enabled', 'no');
    if ($smtp_enabled !== 'yes') {
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host       = get_option('school_smtp_host');
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = get_option('school_smtp_port', '587');
    $phpmailer->Username   = get_option('school_smtp_username');
    $phpmailer->Password   = get_option('school_smtp_password');
    
    $encryption = get_option('school_smtp_encryption', 'tls');
    if ($encryption === 'ssl') {
        $phpmailer->SMTPSecure = 'ssl';
    } elseif ($encryption === 'tls') {
        $phpmailer->SMTPSecure = 'tls';
    } else {
        $phpmailer->SMTPSecure = '';
    }

    $from_email = get_option('school_smtp_from_email');
    if (!empty($from_email)) {
        $phpmailer->From = $from_email;
    }
    $from_name = get_option('school_smtp_from_name');
    if (!empty($from_name)) {
        $phpmailer->FromName = $from_name;
    }
}

// 9. Log wp_mail errors to activity log and public diagnostics file
add_action('wp_mail_failed', function($error) {
    if (is_wp_error($error)) {
        \SchoolManagementApi\Services\AuthService::logActivity(null, 'MAIL_FAILED', $error->get_error_message());
        
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

