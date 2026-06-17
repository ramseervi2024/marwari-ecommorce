<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\PurchaseOrderController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class PurchaseorderRoutes {
    
    public static function register() {
        $controller = new PurchaseOrderController();
        $namespace = 'inventory-management/v1';

        // GET /purchase-orders
        register_rest_route($namespace, '/purchase-orders', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /purchase-orders/:id
        register_rest_route($namespace, '/purchase-orders/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /purchase-orders
        register_rest_route($namespace, '/purchase-orders', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_purchase_orders')
        ]);

        // PUT /purchase-orders/:id
        register_rest_route($namespace, '/purchase-orders/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_purchase_orders')
        ]);

        // DELETE /purchase-orders/:id
        register_rest_route($namespace, '/purchase-orders/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_purchase_orders')
        ]);
    }
}
