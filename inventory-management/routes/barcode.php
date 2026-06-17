<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\BarcodeController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class BarcodeRoutes {
    
    public static function register() {
        $controller = new BarcodeController();
        $namespace = 'inventory-management/v1';

        // POST /barcode/generate
        register_rest_route($namespace, '/barcode/generate', [
            'methods' => 'POST',
            'callback' => [$controller, 'generate'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /barcode/:code
        register_rest_route($namespace, '/barcode/(?P<code>[a-zA-Z0-9\-]+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'lookup'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
