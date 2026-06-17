<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\WarehouseController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class WarehouseRoutes {
    
    public static function register() {
        $controller = new WarehouseController();
        $namespace = 'inventory-management/v1';

        // GET /warehouses
        register_rest_route($namespace, '/warehouses', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /warehouses/:id
        register_rest_route($namespace, '/warehouses/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /warehouses
        register_rest_route($namespace, '/warehouses', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_warehouses')
        ]);

        // PUT /warehouses/:id
        register_rest_route($namespace, '/warehouses/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_warehouses')
        ]);

        // DELETE /warehouses/:id
        register_rest_route($namespace, '/warehouses/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_warehouses')
        ]);
    }
}
