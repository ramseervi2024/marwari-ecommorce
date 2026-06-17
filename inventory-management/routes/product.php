<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\ProductController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class ProductRoutes {
    
    public static function register() {
        $controller = new ProductController();
        $namespace = 'inventory-management/v1';

        // GET /products
        register_rest_route($namespace, '/products', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /products/:id
        register_rest_route($namespace, '/products/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /products
        register_rest_route($namespace, '/products', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);

        // PUT /products/:id
        register_rest_route($namespace, '/products/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);

        // DELETE /products/:id
        register_rest_route($namespace, '/products/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);
    }
}
