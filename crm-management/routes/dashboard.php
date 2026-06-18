<?php
namespace CrmManagementApi\Routes;

use CrmManagementApi\Controllers\DashboardController;
use CrmManagementApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) {
    exit;
}

class DashboardRoutes {
    public static function register() {
        $namespace = 'crm/v1';

        register_rest_route($namespace, '/dashboard/stats', [
            'methods'             => 'GET',
            'callback'            => [new DashboardController(), 'getStats'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/dashboard/activity-logs', [
            'methods'             => 'GET',
            'callback'            => [new DashboardController(), 'getActivityLogs'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);
    }
}
