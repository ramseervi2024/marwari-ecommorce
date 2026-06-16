<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\ReportsController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class ReportsRoutes {
    public static function register() {
        $controller = new ReportsController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/reports/costing', [
            'methods' => 'GET',
            'callback' => [$controller, 'getCostingReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/reports/profitability', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProfitabilityReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/reports/orders', [
            'methods' => 'GET',
            'callback' => [$controller, 'getOrdersReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/reports/production', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProductionReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/reports/fabric', [
            'methods' => 'GET',
            'callback' => [$controller, 'getFabricReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/reports/workers', [
            'methods' => 'GET',
            'callback' => [$controller, 'getWorkersReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/reports/quality', [
            'methods' => 'GET',
            'callback' => [$controller, 'getQualityReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/reports/wastage', [
            'methods' => 'GET',
            'callback' => [$controller, 'getWastageReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/reports/dispatch', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDispatchReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/reports/profit-loss', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProfitLossReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
