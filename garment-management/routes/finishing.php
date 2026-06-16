<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\FinishingController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class FinishingRoutes {
    public static function register() {
        $controller = new FinishingController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/finishing', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/finishing', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/finishing/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/finishing/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/finishing/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);
    }
}
