<?php
namespace TransportManagementApi\Routes;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Controllers\DeliveryController;
use TransportManagementApi\Middleware\RoleMiddleware;

class DeliveryRoutes {
    
    public static function register() {
        $controller = new DeliveryController();
        $namespace = 'transport-management/v1';

        register_rest_route($namespace, '/deliveries', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/deliveries/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/deliveries', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_deliveries')
        ]);

        register_rest_route($namespace, '/deliveries/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => function($request) {
                return current_user_can('manage_deliveries') || current_user_can('update_delivery_status') || current_user_can('manage_transport');
            }
        ]);

        register_rest_route($namespace, '/deliveries/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_deliveries')
        ]);
    }
}
