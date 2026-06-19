<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\VendorController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class VendorRoutes {
    public static function register() {
        $controller = new VendorController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/vendors', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vendors')
        ]);
        register_rest_route($namespace, '/vendors', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vendors')
        ]);
        register_rest_route($namespace, '/vendors/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vendors')
        ]);
        register_rest_route($namespace, '/vendors/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vendors')
        ]);
        register_rest_route($namespace, '/vendors/payments', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexPayments'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vendors')
        ]);
        register_rest_route($namespace, '/vendors/payments', [
            'methods' => 'POST',
            'callback' => [$controller, 'createPayment'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vendors')
        ]);
        register_rest_route($namespace, '/vendors/payments/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updatePayment'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vendors')
        ]);
        register_rest_route($namespace, '/vendors/payments/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deletePayment'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_vendors')
        ]);
    }
}
