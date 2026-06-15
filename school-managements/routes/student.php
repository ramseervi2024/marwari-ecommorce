<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\StudentController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class StudentRoutes {
    
    public static function register() {
        $controller = new StudentController();
        $namespace = 'school-management/v1';

        // GET /students
        register_rest_route($namespace, '/students', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_students')
        ]);

        // POST /students
        register_rest_route($namespace, '/students', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_students')
        ]);

        // GET /students/{id}
        register_rest_route($namespace, '/students/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'show'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_students')
        ]);

        // PUT /students/{id}
        register_rest_route($namespace, '/students/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_students')
        ]);

        // DELETE /students/{id}
        register_rest_route($namespace, '/students/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_students')
        ]);
    }
}
