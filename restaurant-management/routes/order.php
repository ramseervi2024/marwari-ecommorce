<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\OrderController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class OrderRoutes {
    public static function register() {
        $controller = new OrderController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/orders', [
            'methods' => 'GET',
            'callback' => [$controller, 'getOrders'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/orders/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getOrder'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/orders', [
            'methods' => 'POST',
            'callback' => [$controller, 'createOrder'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_orders')
        ]);
        register_rest_route($namespace, '/orders/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateOrder'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_orders')
        ]);
        register_rest_route($namespace, '/orders/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteOrder'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_orders')
        ]);
    }
}
