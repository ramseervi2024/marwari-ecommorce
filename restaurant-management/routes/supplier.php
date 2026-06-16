<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\SupplierController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class SupplierRoutes {
    public static function register() {
        $controller = new SupplierController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/suppliers', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSuppliers'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/suppliers', [
            'methods' => 'POST',
            'callback' => [$controller, 'createSupplier'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);
        register_rest_route($namespace, '/suppliers/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateSupplier'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);
        register_rest_route($namespace, '/suppliers/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteSupplier'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);
    }
}
