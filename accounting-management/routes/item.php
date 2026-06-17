<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\ItemController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class ItemRoutes {
    
    public static function register() {
        $controller = new ItemController();
        $namespace = 'accounting-management/v1';

        // GET /items
        register_rest_route($namespace, '/items', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /items/:id
        register_rest_route($namespace, '/items/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /items
        register_rest_route($namespace, '/items', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_items')
        ]);

        // PUT /items/:id
        register_rest_route($namespace, '/items/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_items')
        ]);

        // DELETE /items/:id
        register_rest_route($namespace, '/items/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_items')
        ]);
    }
}
