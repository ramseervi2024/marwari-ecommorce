<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\MenuController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class MenuRoutes {
    public static function register() {
        $controller = new MenuController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/menu', [
            'methods' => 'GET',
            'callback' => [$controller, 'getMenu'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/menu/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getMenuItem'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/menu', [
            'methods' => 'POST',
            'callback' => [$controller, 'createMenuItem'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
        register_rest_route($namespace, '/menu/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateMenuItem'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
        register_rest_route($namespace, '/menu/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteMenuItem'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
    }
}
