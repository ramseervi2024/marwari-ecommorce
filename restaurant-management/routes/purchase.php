<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\PurchaseController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class PurchaseRoutes {
    public static function register() {
        $controller = new PurchaseController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/purchases', [
            'methods' => 'GET',
            'callback' => [$controller, 'getPurchases'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/purchases', [
            'methods' => 'POST',
            'callback' => [$controller, 'createPurchase'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);
        register_rest_route($namespace, '/purchases/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deletePurchase'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_inventory')
        ]);
    }
}
