<?php
namespace TransportManagementApi\Routes;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Controllers\RouteController;
use TransportManagementApi\Middleware\RoleMiddleware;

class RouteRoutes {
    
    public static function register() {
        $controller = new RouteController();
        $namespace = 'transport-management/v1';

        register_rest_route($namespace, '/routes', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/routes/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/routes', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_routes')
        ]);

        register_rest_route($namespace, '/routes/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_routes')
        ]);

        register_rest_route($namespace, '/routes/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_routes')
        ]);
    }
}
