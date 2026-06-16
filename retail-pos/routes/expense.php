<?php
namespace RetailPosApi\Routes;

use RetailPosApi\Controllers\ExpenseController;
use RetailPosApi\Middleware\RoleMiddleware;

class ExpenseRoutes {
    public static function register() {
        $controller = new ExpenseController();
        $namespace = 'retail-pos/v1';

        register_rest_route($namespace, '/expenses', [
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

        register_rest_route($namespace, '/expenses/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_sales')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_sales')
            ]
        ]);
    }
}
