<?php
namespace TransportManagementApi\Routes;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Controllers\DriverController;
use TransportManagementApi\Middleware\RoleMiddleware;

class DriverRoutes {
    
    public static function register() {
        $controller = new DriverController();
        $namespace = 'transport-management/v1';

        register_rest_route($namespace, '/drivers', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/drivers/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/drivers', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vehicles')
        ]);

        register_rest_route($namespace, '/drivers/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vehicles')
        ]);

        register_rest_route($namespace, '/drivers/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vehicles')
        ]);
    }
}
