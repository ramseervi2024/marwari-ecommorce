<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\ReportsController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class ReportsRoutes {
    
    public static function register() {
        $controller = new ReportsController();
        $namespace = 'inventory-management/v1';

        // GET /reports/stock-valuation
        register_rest_route($namespace, '/reports/stock-valuation', [
            'methods' => 'GET',
            'callback' => [$controller, 'getStockValuationReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/low-stock
        register_rest_route($namespace, '/reports/low-stock', [
            'methods' => 'GET',
            'callback' => [$controller, 'getLowStockReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/stock-movements
        register_rest_route($namespace, '/reports/stock-movements', [
            'methods' => 'GET',
            'callback' => [$controller, 'getStockMovementReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/audit-variances
        register_rest_route($namespace, '/reports/audit-variances', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAuditVarianceReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
    }
}
