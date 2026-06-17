<?php
namespace HrManagementApi\Routes;

use HrManagementApi\Controllers\DashboardController;
use HrManagementApi\Middleware\AuthMiddleware;
use HrManagementApi\Middleware\RoleMiddleware;

class DashboardRoutes {
    public static function register() {
        $namespace  = 'hr-management/v1';
        $controller = new DashboardController();
        $auth       = [AuthMiddleware::class, 'authenticate'];

        register_rest_route($namespace, '/dashboard/stats', [
            'methods'             => 'GET',
            'callback'            => [$controller, 'getStats'],
            'permission_callback' => $auth,
        ]);

        register_rest_route($namespace, '/dashboard/activity-logs', [
            'methods'             => 'GET',
            'callback'            => [$controller, 'getActivityLogs'],
            'permission_callback' => function($request) {
                return AuthMiddleware::authenticate($request) && RoleMiddleware::requireCapability($request, 'manage_hr_users');
            },
        ]);
    }
}
