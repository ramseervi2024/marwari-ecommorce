<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\ReportsController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class ReportsRoutes {
    
    public static function register() {
        $controller = new ReportsController();
        $namespace = 'accounting-management/v1';

        // GET /reports/profit-loss
        register_rest_route($namespace, '/reports/profit-loss', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProfitLoss'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/balance-sheet
        register_rest_route($namespace, '/reports/balance-sheet', [
            'methods' => 'GET',
            'callback' => [$controller, 'getBalanceSheet'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/gst-summary
        register_rest_route($namespace, '/reports/gst-summary', [
            'methods' => 'GET',
            'callback' => [$controller, 'getGstSummary'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
    }
}
