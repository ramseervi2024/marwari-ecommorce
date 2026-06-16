<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\SupplierController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class SupplierRoutes {
    public static function register() {
        $controller = new SupplierController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/supplier', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);

        register_rest_route($namespace, '/supplier', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);

        register_rest_route($namespace, '/supplier/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);

        register_rest_route($namespace, '/supplier/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);

        register_rest_route($namespace, '/supplier/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);
    }
}
