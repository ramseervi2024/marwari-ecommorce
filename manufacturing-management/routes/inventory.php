<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\InventoryController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class InventoryRoutes {
    public static function register() {
        $controller = new InventoryController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/inventory', [
            'methods' => 'GET',
            'callback' => [$controller, 'getInventorySummary'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/inventory/adjustment', [
            'methods' => 'POST',
            'callback' => [$controller, 'createAdjustment'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_store')
        ]);

        register_rest_route($namespace, '/inventory/low-stock', [
            'methods' => 'GET',
            'callback' => [$controller, 'getLowStock'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/inventory/stock-movement', [
            'methods' => 'GET',
            'callback' => [$controller, 'getStockMovement'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
