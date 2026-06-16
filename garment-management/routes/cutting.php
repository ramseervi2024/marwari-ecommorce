<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\CuttingController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class CuttingRoutes {
    public static function register() {
        $controller = new CuttingController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/cutting', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/cutting', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/cutting/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/cutting/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/cutting/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);
    }
}
