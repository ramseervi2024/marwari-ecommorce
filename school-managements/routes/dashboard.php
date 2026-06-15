<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\DashboardController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class DashboardRoutes {
    
    public static function register() {
        $controller = new DashboardController();
        $namespace = 'school-management/v1';

        register_rest_route($namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSummary'],
            'permission_callback' => RoleMiddleware::hasCapability('view_dashboard')
        ]);
    }
}
