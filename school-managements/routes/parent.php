<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\ParentController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class ParentRoutes {
    
    public static function register() {
        $controller = new ParentController();
        $namespace = 'school-management/v1';

        register_rest_route($namespace, '/parents', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_parents')
        ]);

        register_rest_route($namespace, '/parents', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_parents')
        ]);

        register_rest_route($namespace, '/parents/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'show'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_parents')
        ]);

        register_rest_route($namespace, '/parents/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_parents')
        ]);

        register_rest_route($namespace, '/parents/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_parents')
        ]);
    }
}
