<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\DocumentController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class DocumentRoutes {
    
    public static function register() {
        $controller = new DocumentController();
        $namespace = 'accounting-management/v1';

        // GET /documents
        register_rest_route($namespace, '/documents', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /documents
        register_rest_route($namespace, '/documents', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // DELETE /documents/:id
        register_rest_route($namespace, '/documents/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
