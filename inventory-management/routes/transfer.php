<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\TransferController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class TransferRoutes {
    
    public static function register() {
        $controller = new TransferController();
        $namespace = 'inventory-management/v1';

        // GET /transfers
        register_rest_route($namespace, '/transfers', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /transfers/:id
        register_rest_route($namespace, '/transfers/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /transfers
        register_rest_route($namespace, '/transfers', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_transfers')
        ]);

        // PUT /transfers/:id/status
        register_rest_route($namespace, '/transfers/(?P<id>\d+)/status', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateStatus'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_transfers')
        ]);

        // DELETE /transfers/:id
        register_rest_route($namespace, '/transfers/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_transfers')
        ]);
    }
}
