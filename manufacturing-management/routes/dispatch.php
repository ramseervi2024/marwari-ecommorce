<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\DispatchController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class DispatchRoutes {
    public static function register() {
        $controller = new DispatchController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/dispatch', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'index'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_dispatch')
            ]
        ]);

        register_rest_route($namespace, '/dispatch/(?P<id>\d+)', [
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_dispatch')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_dispatch')
            ]
        ]);
    }
}
