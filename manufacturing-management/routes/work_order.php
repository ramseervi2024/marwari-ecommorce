<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\WorkOrderController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class WorkOrderRoutes {
    public static function register() {
        $controller = new WorkOrderController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/work-orders', [
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

        register_rest_route($namespace, '/work-orders/(?P<id>\d+)', [
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
