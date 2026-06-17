<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\LedgerController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class LedgerRoutes {
    
    public static function register() {
        $controller = new LedgerController();
        $namespace = 'accounting-management/v1';

        // GET /ledger
        register_rest_route($namespace, '/ledger', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
