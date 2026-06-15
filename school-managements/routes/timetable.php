<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\TimetableController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class TimetableRoutes {
    
    public static function register() {
        $controller = new TimetableController();
        $namespace = 'school-management/v1';

        register_rest_route($namespace, '/timetable', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/timetable', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_classes')
        ]);

        register_rest_route($namespace, '/timetable/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_classes')
        ]);

        register_rest_route($namespace, '/timetable/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_classes')
        ]);
    }
}
