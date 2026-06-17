<?php
namespace HrManagementApi\Routes;

use HrManagementApi\Controllers\AttendanceController;
use HrManagementApi\Middleware\AuthMiddleware;
use HrManagementApi\Middleware\RoleMiddleware;

class AttendanceRoutes {
    public static function register() {
        $namespace  = 'hr-management/v1';
        $controller = new AttendanceController();
        $auth       = [AuthMiddleware::class, 'authenticate'];

        // List all (admin/manager) or own (employee)
        register_rest_route($namespace, '/attendance', [
            'methods'             => 'GET',
            'callback'            => [$controller, 'getAll'],
            'permission_callback' => $auth,
        ]);

        // Check-in (any authenticated employee)
        register_rest_route($namespace, '/attendance/check-in', [
            'methods'             => 'POST',
            'callback'            => [$controller, 'checkIn'],
            'permission_callback' => $auth,
        ]);

        // Check-out (any authenticated employee)
        register_rest_route($namespace, '/attendance/check-out', [
            'methods'             => 'POST',
            'callback'            => [$controller, 'checkOut'],
            'permission_callback' => $auth,
        ]);

        // Get single record
        register_rest_route($namespace, '/attendance/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [$controller, 'getById'],
                'permission_callback' => $auth,
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [$controller, 'update'],
                'permission_callback' => function($request) {
                    return AuthMiddleware::authenticate($request) && RoleMiddleware::requireCapability($request, 'manage_attendance');
                },
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [$controller, 'delete'],
                'permission_callback' => function($request) {
                    return AuthMiddleware::authenticate($request) && RoleMiddleware::requireCapability($request, 'manage_attendance');
                },
            ],
        ]);
    }
}
