<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\ClientController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class ClientRoutes {
    public static function register() {
        $controller = new ClientController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/clients', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_clients')
        ]);

        register_rest_route($namespace, '/clients', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_clients')
        ]);

        register_rest_route($namespace, '/clients/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_clients')
        ]);

        register_rest_route($namespace, '/clients/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_clients')
        ]);
    }
}
