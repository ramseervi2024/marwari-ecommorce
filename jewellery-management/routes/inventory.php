<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\InventoryController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class InventoryRoutes {
    public static function register() {
        $controller = new InventoryController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/inventory', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/inventory', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_inventory')
        ]);

        register_rest_route($namespace, '/inventory/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/inventory/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_inventory')
        ]);

        register_rest_route($namespace, '/inventory/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_inventory')
        ]);
    }
}
