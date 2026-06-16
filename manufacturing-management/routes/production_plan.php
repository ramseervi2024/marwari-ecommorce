<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\ProductionPlanController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class ProductionPlanRoutes {
    public static function register() {
        $controller = new ProductionPlanController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/production-plans', [
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

        register_rest_route($namespace, '/production-plans/(?P<id>\d+)', [
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_production')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_production')
            ]
        ]);
    }
}
