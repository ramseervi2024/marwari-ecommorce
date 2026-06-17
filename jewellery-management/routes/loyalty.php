<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\LoyaltyController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class LoyaltyRoutes {
    public static function register() {
        $controller = new LoyaltyController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/loyalty', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/loyalty', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_billing')
        ]);

        register_rest_route($namespace, '/loyalty/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/loyalty/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_billing')
        ]);

        register_rest_route($namespace, '/loyalty/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_billing')
        ]);
    }
}
