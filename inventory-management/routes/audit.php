<?php
namespace InventoryManagementApi\Routes;

use InventoryManagementApi\Controllers\AuditController;
use InventoryManagementApi\Middleware\RoleMiddleware;

class AuditRoutes {
    
    public static function register() {
        $controller = new AuditController();
        $namespace = 'inventory-management/v1';

        // GET /audits
        register_rest_route($namespace, '/audits', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /audits
        register_rest_route($namespace, '/audits', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_audits')
        ]);

        // PUT /audits/:id
        register_rest_route($namespace, '/audits/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_audits')
        ]);
    }
}
