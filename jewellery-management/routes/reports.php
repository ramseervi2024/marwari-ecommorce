<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\ReportsController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class ReportsRoutes {
    public static function register() {
        $controller = new ReportsController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/reports/gst', [
            'methods' => 'GET',
            'callback' => [$controller, 'getGstReport'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_reports')
        ]);

        register_rest_route($namespace, '/reports/karigar', [
            'methods' => 'GET',
            'callback' => [$controller, 'getKarigarReport'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_reports')
        ]);

        register_rest_route($namespace, '/reports/sales', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSalesRegistry'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_reports')
        ]);

        register_rest_route($namespace, '/reports/stocks', [
            'methods' => 'GET',
            'callback' => [$controller, 'getStocksValuation'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_reports')
        ]);

        register_rest_route($namespace, '/reports/profit-loss', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProfitLossReport'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_reports')
        ]);
    }
}
