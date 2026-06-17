<?php
namespace TransportManagementApi\Routes;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Controllers\MaintenanceController;
use TransportManagementApi\Middleware\RoleMiddleware;

class MaintenanceRoutes {
    
    public static function register() {
        $controller = new MaintenanceController();
        $namespace = 'transport-management/v1';

        register_rest_route($namespace, '/maintenance', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/maintenance/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/maintenance', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_maintenance')
        ]);

        register_rest_route($namespace, '/maintenance/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_maintenance')
        ]);

        register_rest_route($namespace, '/maintenance/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_maintenance')
        ]);
    }
}
