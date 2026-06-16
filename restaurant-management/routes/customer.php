<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\CustomerController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class CustomerRoutes {
    public static function register() {
        $controller = new CustomerController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/customers', [
            'methods' => 'GET',
            'callback' => [$controller, 'getCustomers'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/customers', [
            'methods' => 'POST',
            'callback' => [$controller, 'createCustomer'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/customers/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateCustomer'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/customers/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteCustomer'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_restaurant')
        ]);
        register_rest_route($namespace, '/loyalty/redeem', [
            'methods' => 'POST',
            'callback' => [$controller, 'redeemLoyaltyPoints'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
