<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\FinishedGoodsController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class FinishedGoodsRoutes {
    public static function register() {
        $controller = new FinishedGoodsController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/finished-goods', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'index'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_store')
            ]
        ]);

        register_rest_route($namespace, '/finished-goods/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'get'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_store')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_store')
            ]
        ]);
    }
}
