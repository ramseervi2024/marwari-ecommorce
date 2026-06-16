<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\WorkerController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class WorkerRoutes {
    public static function register() {
        $controller = new WorkerController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/worker', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_workers')
        ]);

        register_rest_route($namespace, '/worker', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_workers')
        ]);

        register_rest_route($namespace, '/worker/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_workers')
        ]);

        register_rest_route($namespace, '/worker/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_workers')
        ]);

        register_rest_route($namespace, '/worker/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_workers')
        ]);
    }
}
