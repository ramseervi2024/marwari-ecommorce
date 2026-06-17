<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\SupplierController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class SupplierRoutes {
    
    public static function register() {
        $controller = new SupplierController();
        $namespace = 'inventory-management/v1';

        // GET /suppliers
        register_rest_route($namespace, '/suppliers', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /suppliers/:id
        register_rest_route($namespace, '/suppliers/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /suppliers
        register_rest_route($namespace, '/suppliers', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_suppliers')
        ]);

        // PUT /suppliers/:id
        register_rest_route($namespace, '/suppliers/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_suppliers')
        ]);

        // DELETE /suppliers/:id
        register_rest_route($namespace, '/suppliers/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_suppliers')
        ]);
    }
}
