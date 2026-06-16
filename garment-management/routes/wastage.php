<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\WastageController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class WastageRoutes {
    public static function register() {
        $controller = new WastageController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/wastage', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/wastage', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/wastage/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/wastage/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/wastage/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);
    }
}
