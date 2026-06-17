<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\GstController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class GstRoutes {
    
    public static function register() {
        $controller = new GstController();
        $namespace = 'accounting-management/v1';

        // GET /gst
        register_rest_route($namespace, '/gst', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
