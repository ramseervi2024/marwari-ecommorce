<?php
namespace TransportManagementApi\Routes;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Controllers\ChallanController;
use TransportManagementApi\Middleware\RoleMiddleware;

class ChallanRoutes {
    
    public static function register() {
        $controller = new ChallanController();
        $namespace = 'transport-management/v1';

        register_rest_route($namespace, '/challans', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/challans/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/challans', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_challans')
        ]);

        register_rest_route($namespace, '/challans/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_challans')
        ]);

        register_rest_route($namespace, '/challans/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_challans')
        ]);
    }
}
