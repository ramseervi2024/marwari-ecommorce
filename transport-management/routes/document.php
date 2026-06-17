<?php
namespace TransportManagementApi\Routes;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Controllers\DocumentController;
use TransportManagementApi\Middleware\RoleMiddleware;

class DocumentRoutes {
    
    public static function register() {
        $controller = new DocumentController();
        $namespace = 'transport-management/v1';

        register_rest_route($namespace, '/documents', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/documents/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/documents', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/documents/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
