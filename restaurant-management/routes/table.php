<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\TableController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class TableRoutes {
    public static function register() {
        $controller = new TableController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/tables', [
            'methods' => 'GET',
            'callback' => [$controller, 'getTables'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/tables', [
            'methods' => 'POST',
            'callback' => [$controller, 'createTable'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
        register_rest_route($namespace, '/tables/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateTable'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
        register_rest_route($namespace, '/tables/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteTable'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
    }
}
