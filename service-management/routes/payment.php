<?php
namespace ServiceManagementApi\Routes;

use ServiceManagementApi\Controllers\PaymentController;
use ServiceManagementApi\Middleware\RoleMiddleware;

class PaymentRoutes {
    
    public static function register() {
        $controller = new PaymentController();
        $namespace = 'service-management/v1';

        // GET /payments
        register_rest_route($namespace, '/payments', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /payments/:id
        register_rest_route($namespace, '/payments/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /payments
        register_rest_route($namespace, '/payments', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_payments')
        ]);

        // DELETE /payments/:id
        register_rest_route($namespace, '/payments/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_payments')
        ]);
    }
}
