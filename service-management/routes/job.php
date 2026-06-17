<?php
namespace ServiceManagementApi\Routes;

use ServiceManagementApi\Controllers\JobController;
use ServiceManagementApi\Middleware\RoleMiddleware;

class JobRoutes {
    
    public static function register() {
        $controller = new JobController();
        $namespace = 'service-management/v1';

        // GET /jobs
        register_rest_route($namespace, '/jobs', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /jobs/:id
        register_rest_route($namespace, '/jobs/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /jobs
        register_rest_route($namespace, '/jobs', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jobs')
        ]);

        // PUT /jobs/:id
        register_rest_route($namespace, '/jobs/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('update_assigned_jobs')
        ]);

        // DELETE /jobs/:id
        register_rest_route($namespace, '/jobs/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jobs')
        ]);
    }
}
