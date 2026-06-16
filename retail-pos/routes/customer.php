<?php
namespace RetailPosApi\Routes;

use RetailPosApi\Controllers\CustomerController;
use RetailPosApi\Middleware\RoleMiddleware;

class CustomerRoutes {
    public static function register() {
        $controller = new CustomerController();
        $namespace = 'retail-pos/v1';

        register_rest_route($namespace, '/customers', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_customers')
            ]
        ]);

        register_rest_route($namespace, '/customers/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_customers')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_customers')
            ]
        ]);

        register_rest_route($namespace, '/customers/(?P<id>\d+)/points', [
            'methods' => 'GET',
            'callback' => [$controller, 'getPoints'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/loyalty/redeem', [
            'methods' => 'POST',
            'callback' => [$controller, 'redeemPoints'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_customers')
        ]);

        register_rest_route($namespace, '/loyalty', [
            'methods' => 'GET',
            'callback' => [$controller, 'getLoyaltyLedger'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
