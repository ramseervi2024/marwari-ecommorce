<?php
namespace CrmManagementApi\Routes;

use CrmManagementApi\Controllers\CustomerController;
use CrmManagementApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) {
    exit;
}

class CustomerRoutes {
    public static function register() {
        $namespace = 'crm/v1';

        register_rest_route($namespace, '/customers', [
            [
                'methods'             => 'GET',
                'callback'            => [new CustomerController(), 'getCustomers'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [new CustomerController(), 'createCustomer'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        register_rest_route($namespace, '/customers/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [new CustomerController(), 'getCustomer'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [new CustomerController(), 'updateCustomer'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [new CustomerController(), 'deleteCustomer'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);
    }
}
