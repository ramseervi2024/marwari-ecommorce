<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\InventoryController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class InventoryRoutes {
    public static function register() {
        $controller = new InventoryController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/inventory', [
            'methods' => 'GET',
            'callback' => [$controller, 'getInventorySummary'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);
        register_rest_route($namespace, '/inventory/adjustment', [
            'methods' => 'POST',
            'callback' => [$controller, 'createAdjustment'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);
        register_rest_route($namespace, '/inventory/low-stock', [
            'methods' => 'GET',
            'callback' => [$controller, 'getLowStock'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);
        register_rest_route($namespace, '/inventory/movements', [
            'methods' => 'GET',
            'callback' => [$controller, 'getStockMovement'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);
    }
}
