<?php
namespace ServiceManagementApi\Routes;

use ServiceManagementApi\Controllers\QuotationController;
use ServiceManagementApi\Middleware\RoleMiddleware;

class QuotationRoutes {
    
    public static function register() {
        $controller = new QuotationController();
        $namespace = 'service-management/v1';

        // GET /quotations
        register_rest_route($namespace, '/quotations', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /quotations/:id
        register_rest_route($namespace, '/quotations/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /quotations
        register_rest_route($namespace, '/quotations', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_quotations')
        ]);

        // PUT /quotations/:id
        register_rest_route($namespace, '/quotations/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_quotations')
        ]);

        // DELETE /quotations/:id
        register_rest_route($namespace, '/quotations/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_quotations')
        ]);
    }
}
