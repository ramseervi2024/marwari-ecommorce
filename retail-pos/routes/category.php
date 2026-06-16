<?php
namespace RetailPosApi\Routes;

use RetailPosApi\Controllers\CategoryController;
use RetailPosApi\Middleware\RoleMiddleware;

class CategoryRoutes {
    public static function register() {
        $controller = new CategoryController();
        $namespace = 'retail-pos/v1';

        register_rest_route($namespace, '/categories', [
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

        register_rest_route($namespace, '/categories/(?P<id>\d+)', [
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
