<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\RawMaterialController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class RawMaterialRoutes {
    public static function register() {
        $controller = new RawMaterialController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/raw-materials', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'index'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_purchases')
            ]
        ]);

        register_rest_route($namespace, '/raw-materials/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'get'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_purchases')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_purchases')
            ]
        ]);
    }
}
