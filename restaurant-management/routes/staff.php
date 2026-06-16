<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\StaffController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class StaffRoutes {
    public static function register() {
        $controller = new StaffController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/staff', [
            'methods' => 'GET',
            'callback' => [$controller, 'getStaff'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/staff', [
            'methods' => 'POST',
            'callback' => [$controller, 'createStaff'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_staff')
        ]);
        register_rest_route($namespace, '/staff/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateStaff'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_staff')
        ]);
        register_rest_route($namespace, '/staff/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteStaff'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_staff')
        ]);
    }
}
