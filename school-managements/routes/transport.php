<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\TransportController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class TransportRoutes {
    
    public static function register() {
        $controller = new TransportController();
        $namespace = 'school-management/v1';

        register_rest_route($namespace, '/transport', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/transport', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_transport')
        ]);

        register_rest_route($namespace, '/transport/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_transport')
        ]);

        register_rest_route($namespace, '/transport/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_transport')
        ]);
    }
}
