<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\BillingController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class BillingRoutes {
    public static function register() {
        $controller = new BillingController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/billing/invoices', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexInvoices'],
            'permission_callback' => RoleMiddleware::hasCapability('view_billing')
        ]);
        register_rest_route($namespace, '/billing/invoices', [
            'methods' => 'POST',
            'callback' => [$controller, 'createInvoice'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_billing')
        ]);
        register_rest_route($namespace, '/billing/payments', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexPayments'],
            'permission_callback' => RoleMiddleware::hasCapability('view_billing')
        ]);
    }
}
