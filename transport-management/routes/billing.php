<?php
namespace TransportManagementApi\Routes;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Controllers\BillingController;
use TransportManagementApi\Middleware\RoleMiddleware;

class BillingRoutes {
    
    public static function register() {
        $controller = new BillingController();
        $namespace = 'transport-management/v1';

        register_rest_route($namespace, '/billing', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/billing/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/billing/(?P<id>\d+)/pdf', [
            'methods' => 'GET',
            'callback' => [$controller, 'getInvoicePdf'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/billing', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_billing')
        ]);

        register_rest_route($namespace, '/billing/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_billing')
        ]);

        register_rest_route($namespace, '/billing/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_billing')
        ]);
    }
}
