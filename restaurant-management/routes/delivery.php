<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\DeliveryController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class DeliveryRoutes {
    public static function register() {
        $controller = new DeliveryController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/deliveries', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDeliveries'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/deliveries', [
            'methods' => 'POST',
            'callback' => [$controller, 'createDelivery'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_deliveries')
        ]);
        register_rest_route($namespace, '/deliveries/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateDelivery'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_deliveries')
        ]);
        register_rest_route($namespace, '/deliveries/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteDelivery'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_deliveries')
        ]);
    }
}
