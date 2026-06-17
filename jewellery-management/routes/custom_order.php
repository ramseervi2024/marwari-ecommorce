<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\CustomOrderController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class CustomOrderRoutes {
    public static function register() {
        $controller = new CustomOrderController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/custom-order', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/custom-order', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_orders')
        ]);

        register_rest_route($namespace, '/custom-order/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/custom-order/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_orders')
        ]);

        register_rest_route($namespace, '/custom-order/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_orders')
        ]);
    }
}
