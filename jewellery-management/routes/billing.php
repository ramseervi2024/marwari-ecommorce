<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\BillingController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class BillingRoutes {
    public static function register() {
        $controller = new BillingController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/billing', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/billing', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_billing')
        ]);

        register_rest_route($namespace, '/billing/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/billing/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_billing')
        ]);

        register_rest_route($namespace, '/billing/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_billing')
        ]);
    }
}
