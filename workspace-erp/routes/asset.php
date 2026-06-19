<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\AssetController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class AssetRoutes {
    public static function register() {
        $controller = new AssetController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/assets', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_assets')
        ]);
        register_rest_route($namespace, '/assets', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_assets')
        ]);
    }
}
