<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\DashboardController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class DashboardRoutes {
    
    public static function register() {
        $controller = new DashboardController();
        $namespace = 'real-estate-management/v1';

        // GET /dashboard
        register_rest_route($namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDashboardData'],
            'permission_callback' => RoleMiddleware::hasCapability('view_dashboard')
        ]);
    }
}
