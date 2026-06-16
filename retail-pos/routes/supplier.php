<?php
namespace RetailPosApi\Routes;

use RetailPosApi\Controllers\SupplierController;
use RetailPosApi\Middleware\RoleMiddleware;

class SupplierRoutes {
    public static function register() {
        $controller = new SupplierController();
        $namespace = 'retail-pos/v1';

        register_rest_route($namespace, '/suppliers', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_suppliers')
            ]
        ]);

        register_rest_route($namespace, '/suppliers/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_suppliers')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_suppliers')
            ]
        ]);
    }
}
