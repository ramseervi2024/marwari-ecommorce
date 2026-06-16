<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\BillingController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class BillingRoutes {
    public static function register() {
        $controller = new BillingController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/billing', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_billing')
            ]
        ]);

        register_rest_route($namespace, '/billing/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_billing')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_billing')
            ]
        ]);
    }
}
