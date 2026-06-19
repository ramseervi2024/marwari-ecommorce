<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\SustainabilityController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class SustainabilityRoutes {
    public static function register() {
        $controller = new SustainabilityController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/sustainability/energy', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexEnergy'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_sustainability')
        ]);
        register_rest_route($namespace, '/sustainability/energy', [
            'methods' => 'POST',
            'callback' => [$controller, 'createEnergy'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_sustainability')
        ]);
    }
}
