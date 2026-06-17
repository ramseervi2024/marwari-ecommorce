<?php
namespace ConstructionManagementApi\Routes;

use ConstructionManagementApi\Controllers\BillingController;
use ConstructionManagementApi\Middleware\RoleMiddleware;

class BillingRoutes {
    
    public static function register() {
        $controller = new BillingController();
        $namespace = 'construction-management/v1';

        // GET /billing
        register_rest_route($namespace, '/billing', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /billing/:id
        register_rest_route($namespace, '/billing/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /billing
        register_rest_route($namespace, '/billing', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_billing')
        ]);

        // PUT /billing/:id
        register_rest_route($namespace, '/billing/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_billing')
        ]);

        // DELETE /billing/:id
        register_rest_route($namespace, '/billing/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_billing')
        ]);
    }
}
