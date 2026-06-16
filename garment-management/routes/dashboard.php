<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\DashboardController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class DashboardRoutes {
    public static function register() {
        $controller = new DashboardController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDashboardStats'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
