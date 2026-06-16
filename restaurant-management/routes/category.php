<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\CategoryController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class CategoryRoutes {
    public static function register() {
        $controller = new CategoryController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/categories', [
            'methods' => 'GET',
            'callback' => [$controller, 'getCategories'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/categories', [
            'methods' => 'POST',
            'callback' => [$controller, 'createCategory'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
        register_rest_route($namespace, '/categories/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateCategory'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
        register_rest_route($namespace, '/categories/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteCategory'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
    }
}
