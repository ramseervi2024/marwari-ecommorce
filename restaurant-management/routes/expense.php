<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\ExpenseController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class ExpenseRoutes {
    public static function register() {
        $controller = new ExpenseController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/expenses', [
            'methods' => 'GET',
            'callback' => [$controller, 'getExpenses'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/expenses', [
            'methods' => 'POST',
            'callback' => [$controller, 'createExpense'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
        register_rest_route($namespace, '/expenses/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateExpense'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
        register_rest_route($namespace, '/expenses/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteExpense'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
    }
}
