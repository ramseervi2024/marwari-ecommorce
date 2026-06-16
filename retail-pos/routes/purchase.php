<?php
namespace RetailPosApi\Routes;

use RetailPosApi\Controllers\PurchaseController;
use RetailPosApi\Middleware\RoleMiddleware;

class PurchaseRoutes {
    public static function register() {
        $controller = new PurchaseController();
        $namespace = 'retail-pos/v1';

        register_rest_route($namespace, '/purchases', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
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
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
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
