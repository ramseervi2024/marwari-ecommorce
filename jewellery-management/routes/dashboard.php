<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\DashboardController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class DashboardRoutes {
    public static function register() {
        $controller = new DashboardController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDashboardStats'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
