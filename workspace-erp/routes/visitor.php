<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\VisitorController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class VisitorRoutes {
    public static function register() {
        $controller = new VisitorController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/visitors', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_visitors')
        ]);
        register_rest_route($namespace, '/visitors', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_visitors')
        ]);
        register_rest_route($namespace, '/visitors/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_visitors')
        ]);
        register_rest_route($namespace, '/visitors/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_visitors')
        ]);
    }
}
