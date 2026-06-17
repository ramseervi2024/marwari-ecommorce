<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\EwaybillController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class EwaybillRoutes {
    
    public static function register() {
        $controller = new EwaybillController();
        $namespace = 'accounting-management/v1';

        // GET /ewaybill
        register_rest_route($namespace, '/ewaybill', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /ewaybill/generate
        register_rest_route($namespace, '/ewaybill/generate', [
            'methods' => 'POST',
            'callback' => [$controller, 'generate'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_ewaybill')
        ]);
    }
}
