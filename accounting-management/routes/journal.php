<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\JournalController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class JournalRoutes {
    
    public static function register() {
        $controller = new JournalController();
        $namespace = 'accounting-management/v1';

        // GET /journals
        register_rest_route($namespace, '/journals', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /journals/:id
        register_rest_route($namespace, '/journals/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /journals
        register_rest_route($namespace, '/journals', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_journals')
        ]);

        // DELETE /journals/:id
        register_rest_route($namespace, '/journals/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_journals')
        ]);
    }
}
