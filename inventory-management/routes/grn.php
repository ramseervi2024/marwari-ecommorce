<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\GrnController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class GrnRoutes {
    
    public static function register() {
        $controller = new GrnController();
        $namespace = 'inventory-management/v1';

        // GET /grn
        register_rest_route($namespace, '/grn', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /grn
        register_rest_route($namespace, '/grn', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_grn')
        ]);
    }
}
