<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\DashboardController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class DashboardRoutes {
    public static function register() {
        $controller = new DashboardController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSummary'],
            'permission_callback' => RoleMiddleware::hasCapability('view_dashboard')
        ]);
    }
}
