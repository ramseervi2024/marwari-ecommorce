<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\PaymentScheduleController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class PaymentScheduleRoutes {
    
    public static function register() {
        $controller = new PaymentScheduleController();
        $namespace = 'real-estate-management/v1';

        // GET /payment-schedules
        register_rest_route($namespace, '/payment-schedules', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /payment-schedules/:id
        register_rest_route($namespace, '/payment-schedules/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /payment-schedules
        register_rest_route($namespace, '/payment-schedules', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_payments')
        ]);

        // PUT /payment-schedules/:id
        register_rest_route($namespace, '/payment-schedules/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_payments')
        ]);

        // DELETE /payment-schedules/:id
        register_rest_route($namespace, '/payment-schedules/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_payments')
        ]);
    }
}
