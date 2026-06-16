<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\RecipeController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class RecipeRoutes {
    public static function register() {
        $controller = new RecipeController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/recipes', [
            'methods' => 'GET',
            'callback' => [$controller, 'getRecipes'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/recipes/(?P<menu_item_id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getMenuItemRecipe'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/recipes', [
            'methods' => 'POST',
            'callback' => [$controller, 'saveRecipe'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);
        register_rest_route($namespace, '/recipes/(?P<menu_item_id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteRecipe'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);
    }
}
