<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\ProductionController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class ProductionRoutes {
    public static function register() {
        $controller = new ProductionController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/production', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'index'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_production')
            ]
        ]);

        register_rest_route($namespace, '/production/(?P<id>\d+)', [
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_production')
            ]
        ]);
    }
}
