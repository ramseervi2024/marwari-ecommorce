<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\NotificationController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class NotificationRoutes {
    public static function register() {
        $controller = new NotificationController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/notifications', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/notifications', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_notifications')
        ]);
    }
}
