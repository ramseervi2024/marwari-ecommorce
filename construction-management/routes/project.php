<?php
namespace ConstructionManagementApi\Routes;

use ConstructionManagementApi\Controllers\ProjectController;
use ConstructionManagementApi\Middleware\RoleMiddleware;

class ProjectRoutes {
    
    public static function register() {
        $controller = new ProjectController();
        $namespace = 'construction-management/v1';

        // GET /projects
        register_rest_route($namespace, '/projects', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /projects/:id
        register_rest_route($namespace, '/projects/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /projects
        register_rest_route($namespace, '/projects', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_projects')
        ]);

        // PUT /projects/:id
        register_rest_route($namespace, '/projects/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_projects')
        ]);

        // DELETE /projects/:id
        register_rest_route($namespace, '/projects/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_projects')
        ]);

        // --- MILESTONES ---

        // GET /milestones
        register_rest_route($namespace, '/milestones', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAllMilestones'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /milestones
        register_rest_route($namespace, '/milestones', [
            'methods' => 'POST',
            'callback' => [$controller, 'createMilestone'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_projects')
        ]);

        // PUT /milestones/:id
        register_rest_route($namespace, '/milestones/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateMilestone'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_projects')
        ]);

        // DELETE /milestones/:id
        register_rest_route($namespace, '/milestones/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteMilestone'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_projects')
        ]);
    }
}
