<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\HostelController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class HostelRoutes {
    
    public static function register() {
        $controller = new HostelController();
        $namespace = 'school-management/v1';

        register_rest_route($namespace, '/hostels', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/hostels', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);

        register_rest_route($namespace, '/hostels/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);

        register_rest_route($namespace, '/hostels/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);
    }
}
