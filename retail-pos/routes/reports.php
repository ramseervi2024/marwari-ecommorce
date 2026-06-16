<?php
namespace RetailPosApi\Routes;

use RetailPosApi\Controllers\ReportsController;
use RetailPosApi\Middleware\RoleMiddleware;

class ReportsRoutes {
    public static function register() {
        $controller = new ReportsController();
        $namespace = 'retail-pos/v1';

        register_rest_route($namespace, '/reports/sales', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSales'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        register_rest_route($namespace, '/reports/gst', [
            'methods' => 'GET',
            'callback' => [$controller, 'getGst'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        register_rest_route($namespace, '/reports/profit-loss', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProfitLoss'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
    }
}
