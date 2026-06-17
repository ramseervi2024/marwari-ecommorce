<?php
namespace ServiceManagementApi\Routes;

use ServiceManagementApi\Controllers\DashboardController;
use ServiceManagementApi\Middleware\RoleMiddleware;

class DashboardRoutes {
    
    public static function register() {
        $controller = new DashboardController();
        $namespace = 'service-management/v1';

        // GET /dashboard
        register_rest_route($namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDashboardData'],
            'permission_callback' => RoleMiddleware::hasCapability('view_service_dashboard')
        ]);
    }
}
