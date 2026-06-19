<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\OccupancyController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class OccupancyRoutes {
    public static function register() {
        $controller = new OccupancyController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/occupancy', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/occupancy', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_workspaces')
        ]);
    }
}
