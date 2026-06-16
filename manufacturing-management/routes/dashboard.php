<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\DashboardController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class DashboardRoutes {
    public static function register() {
        $controller = new DashboardController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDashboardStats'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
