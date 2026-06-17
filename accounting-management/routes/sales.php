<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\SalesController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class SalesRoutes {
    
    public static function register() {
        $controller = new SalesController();
        $namespace = 'accounting-management/v1';

        // GET /sales
        register_rest_route($namespace, '/sales', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /sales/:id
        register_rest_route($namespace, '/sales/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /sales
        register_rest_route($namespace, '/sales', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_sales')
        ]);

        // DELETE /sales/:id
        register_rest_route($namespace, '/sales/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_sales')
        ]);
    }
}
