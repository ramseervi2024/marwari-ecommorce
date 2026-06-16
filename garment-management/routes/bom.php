<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\BomController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class BomRoutes {
    public static function register() {
        $controller = new BomController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/bom', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);
        register_rest_route($namespace, '/bom', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);
        register_rest_route($namespace, '/bom/product/(?P<product_id>[a-zA-Z0-9_-]+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getByProduct'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);
        register_rest_route($namespace, '/bom/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);
        register_rest_route($namespace, '/bom/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);
        register_rest_route($namespace, '/bom/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);
    }
}
