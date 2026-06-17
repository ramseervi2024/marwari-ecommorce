<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\DashboardController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class DashboardRoutes {
    
    public static function register() {
        $controller = new DashboardController();
        $namespace = 'inventory-management/v1';

        // GET /dashboard
        register_rest_route($namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDashboardData'],
            'permission_callback' => RoleMiddleware::hasCapability('view_dashboard')
        ]);
    }
}
