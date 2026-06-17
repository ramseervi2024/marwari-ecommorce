<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\PurchaseController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class PurchaseRoutes {
    
    public static function register() {
        $controller = new PurchaseController();
        $namespace = 'accounting-management/v1';

        // GET /purchases
        register_rest_route($namespace, '/purchases', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /purchases/:id
        register_rest_route($namespace, '/purchases/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /purchases
        register_rest_route($namespace, '/purchases', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_purchases')
        ]);

        // DELETE /purchases/:id
        register_rest_route($namespace, '/purchases/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_purchases')
        ]);
    }
}
