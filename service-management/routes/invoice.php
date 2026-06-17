<?php
namespace ServiceManagementApi\Routes;

use ServiceManagementApi\Controllers\InvoiceController;
use ServiceManagementApi\Middleware\RoleMiddleware;

class InvoiceRoutes {
    
    public static function register() {
        $controller = new InvoiceController();
        $namespace = 'service-management/v1';

        // GET /invoices
        register_rest_route($namespace, '/invoices', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /invoices/:id
        register_rest_route($namespace, '/invoices/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /invoices
        register_rest_route($namespace, '/invoices', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_invoices')
        ]);

        // PUT /invoices/:id
        register_rest_route($namespace, '/invoices/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_invoices')
        ]);

        // DELETE /invoices/:id
        register_rest_route($namespace, '/invoices/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_invoices')
        ]);
    }
}
