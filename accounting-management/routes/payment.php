<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\PaymentController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class PaymentRoutes {
    
    public static function register() {
        $controller = new PaymentController();
        $namespace = 'accounting-management/v1';

        // GET /payment
        register_rest_route($namespace, '/payment', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /payment/:id
        register_rest_route($namespace, '/payment/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /payment
        register_rest_route($namespace, '/payment', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_payments')
        ]);
    }
}
