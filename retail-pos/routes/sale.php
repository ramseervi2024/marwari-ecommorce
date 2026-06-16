<?php
namespace RetailPosApi\Routes;

use RetailPosApi\Controllers\SaleController;
use RetailPosApi\Middleware\RoleMiddleware;

class SaleRoutes {
    public static function register() {
        $controller = new SaleController();
        $namespace = 'retail-pos/v1';

        register_rest_route($namespace, '/sales', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_sales')
            ]
        ]);

        register_rest_route($namespace, '/sales/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_sales')
            ]
        ]);
    }
}
