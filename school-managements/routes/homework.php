<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\HomeworkController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class HomeworkRoutes {
    
    public static function register() {
        $controller = new HomeworkController();
        $namespace = 'school-management/v1';

        register_rest_route($namespace, '/homework', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/homework', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_homework')
        ]);

        register_rest_route($namespace, '/homework/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_homework')
        ]);

        register_rest_route($namespace, '/homework/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_homework')
        ]);

        register_rest_route($namespace, '/homework/submit', [
            'methods' => 'POST',
            'callback' => [$controller, 'submit'],
            'permission_callback' => RoleMiddleware::hasCapability('download_documents')
        ]);
    }
}
