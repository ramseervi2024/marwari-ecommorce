<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\DashboardController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class DashboardRoutes {
    public static function register() {
        $controller = new DashboardController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDashboardStats'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
