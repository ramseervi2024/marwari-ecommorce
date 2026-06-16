<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\QualityController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class QualityRoutes {
    public static function register() {
        $controller = new QualityController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/quality', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_quality')
        ]);

        register_rest_route($namespace, '/quality', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_quality')
        ]);

        register_rest_route($namespace, '/quality/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_quality')
        ]);

        register_rest_route($namespace, '/quality/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_quality')
        ]);

        register_rest_route($namespace, '/quality/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_quality')
        ]);
    }
}
