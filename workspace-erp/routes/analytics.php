<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\AnalyticsController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class AnalyticsRoutes {
    public static function register() {
        $controller = new AnalyticsController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/analytics/utilization', [
            'methods' => 'GET',
            'callback' => [$controller, 'getUtilization'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/analytics/sla', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSlaCompliance'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
    }
}
