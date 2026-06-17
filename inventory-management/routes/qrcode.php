<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\QrcodeController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class QrcodeRoutes {
    
    public static function register() {
        $controller = new QrcodeController();
        $namespace = 'inventory-management/v1';

        // POST /qrcode/generate
        register_rest_route($namespace, '/qrcode/generate', [
            'methods' => 'POST',
            'callback' => [$controller, 'generate'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /qrcode/:code
        register_rest_route($namespace, '/qrcode/(?P<code>[a-zA-Z0-9\-]+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'lookup'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
