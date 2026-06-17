<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\DamageController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class DamageRoutes {
    
    public static function register() {
        $controller = new DamageController();
        $namespace = 'inventory-management/v1';

        // GET /damaged-stock
        register_rest_route($namespace, '/damaged-stock', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /damaged-stock
        register_rest_route($namespace, '/damaged-stock', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_damaged_stock')
        ]);

        // PUT /damaged-stock/:id
        register_rest_route($namespace, '/damaged-stock/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_damaged_stock')
        ]);
    }
}
