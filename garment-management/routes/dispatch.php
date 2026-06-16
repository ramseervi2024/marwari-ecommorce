<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\DispatchController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class DispatchRoutes {
    public static function register() {
        $controller = new DispatchController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/dispatch', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_dispatch')
        ]);

        register_rest_route($namespace, '/dispatch', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_dispatch')
        ]);

        register_rest_route($namespace, '/dispatch/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_dispatch')
        ]);

        register_rest_route($namespace, '/dispatch/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_dispatch')
        ]);

        register_rest_route($namespace, '/dispatch/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_dispatch')
        ]);
    }
}
