<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\ExpenseController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class ExpenseRoutes {
    
    public static function register() {
        $controller = new ExpenseController();
        $namespace = 'accounting-management/v1';

        // GET /expenses
        register_rest_route($namespace, '/expenses', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /expenses/:id
        register_rest_route($namespace, '/expenses/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /expenses
        register_rest_route($namespace, '/expenses', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_expenses')
        ]);

        // DELETE /expenses/:id
        register_rest_route($namespace, '/expenses/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_expenses')
        ]);
    }
}
