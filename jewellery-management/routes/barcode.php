<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\BarcodeController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class BarcodeRoutes {
    public static function register() {
        $controller = new BarcodeController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/barcode/scan', [
            'methods' => 'GET',
            'callback' => [$controller, 'scan'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/barcode/print/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'printLabel'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_inventory')
        ]);
    }
}
