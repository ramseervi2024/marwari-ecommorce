<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\ReportsController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class ReportsRoutes {
    public static function register() {
        $controller = new ReportsController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/reports/revenue', [
            'methods' => 'GET',
            'callback' => [$controller, 'getRevenueReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/occupancy', [
            'methods' => 'GET',
            'callback' => [$controller, 'getOccupancyReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/tickets', [
            'methods' => 'GET',
            'callback' => [$controller, 'getTicketsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/esg', [
            'methods' => 'GET',
            'callback' => [$controller, 'getEsgReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
    }
}
