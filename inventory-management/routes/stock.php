<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\StockController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class StockRoutes {
    
    public static function register() {
        $controller = new StockController();
        $namespace = 'inventory-management/v1';

        // GET /inventory
        register_rest_route($namespace, '/inventory', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /inventory
        register_rest_route($namespace, '/inventory', [
            'methods' => 'POST',
            'callback' => [$controller, 'adjust'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);

        // GET /stock-inward
        register_rest_route($namespace, '/stock-inward', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAllInward'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /stock-inward
        register_rest_route($namespace, '/stock-inward', [
            'methods' => 'POST',
            'callback' => [$controller, 'createInward'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_stock_inward')
        ]);

        // GET /stock-outward
        register_rest_route($namespace, '/stock-outward', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAllOutward'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /stock-outward
        register_rest_route($namespace, '/stock-outward', [
            'methods' => 'POST',
            'callback' => [$controller, 'createOutward'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_stock_outward')
        ]);
    }
}
