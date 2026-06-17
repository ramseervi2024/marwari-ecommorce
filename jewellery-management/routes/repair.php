<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\RepairController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class RepairRoutes {
    public static function register() {
        $controller = new RepairController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/repair', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/repair', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_orders')
        ]);

        register_rest_route($namespace, '/repair/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/repair/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_orders')
        ]);

        register_rest_route($namespace, '/repair/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_orders')
        ]);
    }
}
