<?php
namespace HrManagementApi\Routes;

use HrManagementApi\Controllers\LeaveController;
use HrManagementApi\Middleware\AuthMiddleware;
use HrManagementApi\Middleware\RoleMiddleware;

class LeaveRoutes {
    public static function register() {
        $namespace  = 'hr-management/v1';
        $controller = new LeaveController();
        $auth       = [AuthMiddleware::class, 'authenticate'];

        // List (admin sees all; employee sees own)
        register_rest_route($namespace, '/leaves', [
            [
                'methods'             => 'GET',
                'callback'            => [$controller, 'getAll'],
                'permission_callback' => $auth,
            ],
            [
                'methods'             => 'POST',
                'callback'            => [$controller, 'create'],
                'permission_callback' => $auth,
            ],
        ]);

        register_rest_route($namespace, '/leaves/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [$controller, 'getById'],
                'permission_callback' => $auth,
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [$controller, 'delete'],
                'permission_callback' => $auth,
            ],
        ]);

        // Approve / Reject — Manager / Admin only
        register_rest_route($namespace, '/leaves/(?P<id>\d+)/approve', [
            'methods'             => 'POST',
            'callback'            => [$controller, 'approve'],
            'permission_callback' => function($request) {
                return AuthMiddleware::authenticate($request) && RoleMiddleware::requireCapability($request, 'manage_leaves');
            },
        ]);

        register_rest_route($namespace, '/leaves/(?P<id>\d+)/reject', [
            'methods'             => 'POST',
            'callback'            => [$controller, 'reject'],
            'permission_callback' => function($request) {
                return AuthMiddleware::authenticate($request) && RoleMiddleware::requireCapability($request, 'manage_leaves');
            },
        ]);

        // Leave balances
        register_rest_route($namespace, '/leaves/balance/(?P<employee_id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [$controller, 'getBalance'],
            'permission_callback' => $auth,
        ]);
    }
}
