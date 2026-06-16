<?php
namespace RetailPosApi\Routes;

use RetailPosApi\Controllers\BrandController;
use RetailPosApi\Middleware\RoleMiddleware;

class BrandRoutes {
    public static function register() {
        $controller = new BrandController();
        $namespace = 'retail-pos/v1';

        register_rest_route($namespace, '/brands', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_products')
            ]
        ]);

        register_rest_route($namespace, '/brands/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_products')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_products')
            ]
        ]);
    }
}
