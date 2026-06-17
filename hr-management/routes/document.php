<?php
namespace HrManagementApi\Routes;

use HrManagementApi\Controllers\DocumentController;
use HrManagementApi\Middleware\AuthMiddleware;
use HrManagementApi\Middleware\RoleMiddleware;

class DocumentRoutes {
    public static function register() {
        $namespace  = 'hr-management/v1';
        $controller = new DocumentController();
        $auth       = [AuthMiddleware::class, 'authenticate'];

        register_rest_route($namespace, '/documents', [
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

        register_rest_route($namespace, '/documents/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [$controller, 'getById'],
                'permission_callback' => $auth,
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [$controller, 'update'],
                'permission_callback' => function($request) {
                    return AuthMiddleware::authenticate($request) && RoleMiddleware::requireCapability($request, 'manage_documents');
                },
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [$controller, 'delete'],
                'permission_callback' => function($request) {
                    return AuthMiddleware::authenticate($request) && RoleMiddleware::requireCapability($request, 'manage_documents');
                },
            ],
        ]);
    }
}
