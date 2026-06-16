<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\InvoiceController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class InvoiceRoutes {
    public static function register() {
        $controller = new InvoiceController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/billing', [
            'methods' => 'GET',
            'callback' => [$controller, 'getInvoices'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/billing', [
            'methods' => 'POST',
            'callback' => [$controller, 'createInvoice'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_billing')
        ]);
    }
}
