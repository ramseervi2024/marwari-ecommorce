<?php
namespace ConstructionManagementApi\Routes;

use ConstructionManagementApi\Controllers\DocumentController;
use ConstructionManagementApi\Middleware\RoleMiddleware;

class DocumentRoutes {
    
    public static function register() {
        $controller = new DocumentController();
        $namespace = 'construction-management/v1';

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
            'permission_callback' => RoleMiddleware::hasCapability('manage_projects')
        ]);

        // DELETE /documents/:id
        register_rest_route($namespace, '/documents/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_projects')
        ]);
    }
}
