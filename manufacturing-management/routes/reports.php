<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\ReportsController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class ReportsRoutes {
    public static function register() {
        $controller = new ReportsController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/reports/production-cost', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProductionCostReport'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_mfg_setup')
        ]);

        register_rest_route($namespace, '/reports/material-cost', [
            'methods' => 'GET',
            'callback' => [$controller, 'getMaterialCostReport'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_mfg_setup')
        ]);

        register_rest_route($namespace, '/reports/quality', [
            'methods' => 'GET',
            'callback' => [$controller, 'getQualityReport'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_mfg_setup')
        ]);

        register_rest_route($namespace, '/reports/purchases', [
            'methods' => 'GET',
            'callback' => [$controller, 'getPurchasesReport'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_mfg_setup')
        ]);

        register_rest_route($namespace, '/reports/profit-loss', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProfitLossReport'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_mfg_setup')
        ]);
    }
}
