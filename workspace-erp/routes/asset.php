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
        register_rest_route($namespace, '/assets/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_assets')
        ]);
        register_rest_route($namespace, '/assets/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_assets')
        ]);
        register_rest_route($namespace, '/assets/allocations', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexAllocations'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_assets')
        ]);
        register_rest_route($namespace, '/assets/allocations', [
            'methods' => 'POST',
            'callback' => [$controller, 'createAllocation'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_assets')
        ]);
        register_rest_route($namespace, '/assets/allocations/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateAllocation'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_assets')
        ]);
        register_rest_route($namespace, '/assets/allocations/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteAllocation'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_assets')
        ]);
    }
}
