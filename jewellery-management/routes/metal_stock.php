<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\MetalStockController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class MetalStockRoutes {
    public static function register() {
        $controller = new MetalStockController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/metal-stock', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/metal-stock', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_inventory')
        ]);

        register_rest_route($namespace, '/metal-stock/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/metal-stock/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_inventory')
        ]);

        register_rest_route($namespace, '/metal-stock/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_inventory')
        ]);
    }
}
