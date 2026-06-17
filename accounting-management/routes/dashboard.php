<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\DashboardController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class DashboardRoutes {
    
    public static function register() {
        $controller = new DashboardController();
        $namespace = 'accounting-management/v1';

        // GET /dashboard
        register_rest_route($namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDashboardData'],
            'permission_callback' => RoleMiddleware::hasCapability('view_dashboard')
        ]);
    }
}
