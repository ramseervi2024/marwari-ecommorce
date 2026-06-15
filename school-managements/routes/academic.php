<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\AcademicController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class AcademicRoutes {
    
    public static function register() {
        $controller = new AcademicController();
        $namespace = 'school-management/v1';

        // Classes
        register_rest_route($namespace, '/classes', [
            'methods' => 'GET',
            'callback' => [$controller, 'getClasses'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/classes', [
            'methods' => 'POST',
            'callback' => [$controller, 'createClass'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_classes')
        ]);
        register_rest_route($namespace, '/classes/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateClass'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_classes')
        ]);
        register_rest_route($namespace, '/classes/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteClass'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_classes')
        ]);

        // Sections
        register_rest_route($namespace, '/sections', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSections'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/sections', [
            'methods' => 'POST',
            'callback' => [$controller, 'createSection'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_classes')
        ]);
        register_rest_route($namespace, '/sections/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateSection'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_classes')
        ]);
        register_rest_route($namespace, '/sections/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteSection'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_classes')
        ]);

        // Subjects
        register_rest_route($namespace, '/subjects', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSubjects'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/subjects', [
            'methods' => 'POST',
            'callback' => [$controller, 'createSubject'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_classes')
        ]);
        register_rest_route($namespace, '/subjects/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateSubject'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_classes')
        ]);
        register_rest_route($namespace, '/subjects/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteSubject'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_classes')
        ]);
    }
}
