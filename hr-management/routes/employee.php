<?php
namespace HrManagementApi\Routes;

use HrManagementApi\Controllers\EmployeeController;
use HrManagementApi\Middleware\AuthMiddleware;
use HrManagementApi\Middleware\RoleMiddleware;

class EmployeeRoutes {
    public static function register() {
        $namespace  = 'hr-management/v1';
        $controller = new EmployeeController();
        $auth       = [AuthMiddleware::class, 'authenticate'];

        register_rest_route($namespace, '/employees', [
            'methods'             => 'GET',
            'callback'            => [$controller, 'getAll'],
            'permission_callback' => $auth,
        ]);

        register_rest_route($namespace, '/employees/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [$controller, 'getById'],
                'permission_callback' => $auth,
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [$controller, 'update'],
                'permission_callback' => function($request) {
                    return AuthMiddleware::authenticate($request) && RoleMiddleware::requireCapability($request, 'manage_employees');
                },
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [$controller, 'delete'],
                'permission_callback' => function($request) {
                    return AuthMiddleware::authenticate($request) && RoleMiddleware::requireCapability($request, 'manage_hr_users');
                },
            ],
        ]);
    }
}
