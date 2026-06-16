<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\QualityController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class QualityRoutes {
    public static function register() {
        $controller = new QualityController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/quality', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'index'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_quality')
            ]
        ]);

        register_rest_route($namespace, '/quality/(?P<id>\d+)', [
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_quality')
            ]
        ]);
    }
}
