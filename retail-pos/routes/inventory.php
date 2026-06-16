<?php
namespace RetailPosApi\Routes;

use RetailPosApi\Controllers\InventoryController;
use RetailPosApi\Middleware\RoleMiddleware;

class InventoryRoutes {
    public static function register() {
        $controller = new InventoryController();
        $namespace = 'retail-pos/v1';

        register_rest_route($namespace, '/inventory', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/inventory/adjust', [
            'methods' => 'POST',
            'callback' => [$controller, 'adjustStock'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);

        register_rest_route($namespace, '/inventory/low-stock', [
            'methods' => 'GET',
            'callback' => [$controller, 'getLowStock'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/inventory/out-of-stock', [
            'methods' => 'GET',
            'callback' => [$controller, 'getOutOfStock'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
