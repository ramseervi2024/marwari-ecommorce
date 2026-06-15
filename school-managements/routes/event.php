<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\EventController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class EventRoutes {
    
    public static function register() {
        $controller = new EventController();
        $namespace = 'school-management/v1';

        // Events
        register_rest_route($namespace, '/events', [
            'methods' => 'GET',
            'callback' => [$controller, 'getEvents'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/events', [
            'methods' => 'POST',
            'callback' => [$controller, 'createEvent'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);
        register_rest_route($namespace, '/events/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateEvent'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);
        register_rest_route($namespace, '/events/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteEvent'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);

        // Notices
        register_rest_route($namespace, '/notices', [
            'methods' => 'GET',
            'callback' => [$controller, 'getNotices'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/notices', [
            'methods' => 'POST',
            'callback' => [$controller, 'createNotice'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);
    }
}
