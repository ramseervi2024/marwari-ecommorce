<?php
namespace ConstructionManagementApi\Routes;

use ConstructionManagementApi\Controllers\ContractorController;
use ConstructionManagementApi\Middleware\RoleMiddleware;

class ContractorRoutes {
    
    public static function register() {
        $controller = new ContractorController();
        $namespace = 'construction-management/v1';

        // GET /contractors
        register_rest_route($namespace, '/contractors', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /contractors/:id
        register_rest_route($namespace, '/contractors/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /contractors
        register_rest_route($namespace, '/contractors', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_contractors')
        ]);

        // PUT /contractors/:id
        register_rest_route($namespace, '/contractors/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_contractors')
        ]);

        // DELETE /contractors/:id
        register_rest_route($namespace, '/contractors/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_contractors')
        ]);
    }
}
