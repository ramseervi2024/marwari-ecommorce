<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\VendorController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class VendorRoutes {
    
    public static function register() {
        $controller = new VendorController();
        $namespace = 'accounting-management/v1';

        // GET /vendors
        register_rest_route($namespace, '/vendors', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /vendors/:id
        register_rest_route($namespace, '/vendors/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /vendors
        register_rest_route($namespace, '/vendors', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vendors')
        ]);

        // PUT /vendors/:id
        register_rest_route($namespace, '/vendors/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vendors')
        ]);

        // DELETE /vendors/:id
        register_rest_route($namespace, '/vendors/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vendors')
        ]);
    }
}
