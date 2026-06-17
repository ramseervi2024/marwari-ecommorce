<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\InventoryController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class InventoryRoutes {
    
    public static function register() {
        $controller = new InventoryController();
        $namespace = 'accounting-management/v1';

        // GET /inventory
        register_rest_route($namespace, '/inventory', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /inventory/adjust
        register_rest_route($namespace, '/inventory/adjust', [
            'methods' => 'POST',
            'callback' => [$controller, 'adjust'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);
    }
}
