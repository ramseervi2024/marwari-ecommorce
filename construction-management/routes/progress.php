<?php
namespace ConstructionManagementApi\Routes;

use ConstructionManagementApi\Controllers\ProgressController;
use ConstructionManagementApi\Middleware\RoleMiddleware;

class ProgressRoutes {
    
    public static function register() {
        $controller = new ProgressController();
        $namespace = 'construction-management/v1';

        // GET /progress
        register_rest_route($namespace, '/progress', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /progress/:id
        register_rest_route($namespace, '/progress/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /progress
        register_rest_route($namespace, '/progress', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_progress')
        ]);

        // PUT /progress/:id
        register_rest_route($namespace, '/progress/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_progress')
        ]);

        // DELETE /progress/:id
        register_rest_route($namespace, '/progress/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_progress')
        ]);
    }
}
