<?php
namespace ConstructionManagementApi\Routes;

use ConstructionManagementApi\Controllers\ExpenseController;
use ConstructionManagementApi\Middleware\RoleMiddleware;

class ExpenseRoutes {
    
    public static function register() {
        $controller = new ExpenseController();
        $namespace = 'construction-management/v1';

        // GET /site-expenses
        register_rest_route($namespace, '/site-expenses', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /site-expenses/:id
        register_rest_route($namespace, '/site-expenses/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /site-expenses
        register_rest_route($namespace, '/site-expenses', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_expenses')
        ]);

        // PUT /site-expenses/:id
        register_rest_route($namespace, '/site-expenses/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_expenses')
        ]);

        // DELETE /site-expenses/:id
        register_rest_route($namespace, '/site-expenses/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_expenses')
        ]);
    }
}
