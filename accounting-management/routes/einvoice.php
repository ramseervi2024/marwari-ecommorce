<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\EinvoiceController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class EinvoiceRoutes {
    
    public static function register() {
        $controller = new EinvoiceController();
        $namespace = 'accounting-management/v1';

        // GET /einvoice
        register_rest_route($namespace, '/einvoice', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /einvoice/generate
        register_rest_route($namespace, '/einvoice/generate', [
            'methods' => 'POST',
            'callback' => [$controller, 'generate'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_einvoice')
        ]);
    }
}
