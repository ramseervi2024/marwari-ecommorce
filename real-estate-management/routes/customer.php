<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\CustomerController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class CustomerRoutes {
    
    public static function register() {
        $controller = new CustomerController();
        $namespace = 'real-estate-management/v1';

        // GET /customers
        register_rest_route($namespace, '/customers', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /customers/:id
        register_rest_route($namespace, '/customers/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /customers
        register_rest_route($namespace, '/customers', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_customers')
        ]);

        // PUT /customers/:id
        register_rest_route($namespace, '/customers/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_customers')
        ]);

        // DELETE /customers/:id
        register_rest_route($namespace, '/customers/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_customers')
        ]);
    }
}
