<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\TeacherController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class TeacherRoutes {
    
    public static function register() {
        $controller = new TeacherController();
        $namespace = 'school-management/v1';

        register_rest_route($namespace, '/teachers', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_teachers')
        ]);

        register_rest_route($namespace, '/teachers', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_teachers')
        ]);

        register_rest_route($namespace, '/teachers/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'show'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_teachers')
        ]);

        register_rest_route($namespace, '/teachers/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_teachers')
        ]);

        register_rest_route($namespace, '/teachers/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_teachers')
        ]);
    }
}
