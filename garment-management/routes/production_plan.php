<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\ProductionPlanController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class ProductionPlanRoutes {
    public static function register() {
        $controller = new ProductionPlanController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/production_plan', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/production_plan', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/production_plan/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/production_plan/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/production_plan/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);
    }
}
