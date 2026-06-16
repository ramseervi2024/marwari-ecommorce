<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\OrderController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class OrderRoutes {
    public static function register() {
        $controller = new OrderController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/order', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_orders')
        ]);

        register_rest_route($namespace, '/order', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_orders')
        ]);

        register_rest_route($namespace, '/order/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_orders')
        ]);

        register_rest_route($namespace, '/order/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_orders')
        ]);

        register_rest_route($namespace, '/order/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_orders')
        ]);
    }
}
