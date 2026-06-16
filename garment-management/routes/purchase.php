<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\PurchaseController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class PurchaseRoutes {
    public static function register() {
        $controller = new PurchaseController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/purchase', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);

        register_rest_route($namespace, '/purchase', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);

        register_rest_route($namespace, '/purchase/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);

        register_rest_route($namespace, '/purchase/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);

        register_rest_route($namespace, '/purchase/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_inventory')
        ]);
    }
}
