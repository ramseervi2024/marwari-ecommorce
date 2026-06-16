<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\PurchaseController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class PurchaseRoutes {
    public static function register() {
        $controller = new PurchaseController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/purchases', [
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

        register_rest_route($namespace, '/purchases/(?P<id>\d+)', [
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
