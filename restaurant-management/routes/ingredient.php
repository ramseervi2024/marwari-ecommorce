<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\IngredientController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class IngredientRoutes {
    public static function register() {
        $controller = new IngredientController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/inventory', [
            'methods' => 'GET',
            'callback' => [$controller, 'getIngredients'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/inventory', [
            'methods' => 'POST',
            'callback' => [$controller, 'createIngredient'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);
        register_rest_route($namespace, '/inventory/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateIngredient'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);
        register_rest_route($namespace, '/inventory/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteIngredient'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);
    }
}
