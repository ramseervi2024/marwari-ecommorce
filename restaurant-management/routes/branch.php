<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\BranchController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class BranchRoutes {
    public static function register() {
        $controller = new BranchController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/branches', [
            'methods' => 'GET',
            'callback' => [$controller, 'getBranches'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/branches', [
            'methods' => 'POST',
            'callback' => [$controller, 'createBranch'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
        register_rest_route($namespace, '/branches/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateBranch'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
        register_rest_route($namespace, '/branches/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteBranch'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
    }
}
