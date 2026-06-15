<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\AuthController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class AuthRoutes {
    
    public static function register() {
        $controller = new AuthController();
        $namespace = 'school-management/v1';

        // POST /auth/register
        register_rest_route($namespace, '/auth/register', [
            'methods' => 'POST',
            'callback' => [$controller, 'register'],
            'permission_callback' => '__return_true'
        ]);

        // POST /auth/login
        register_rest_route($namespace, '/auth/login', [
            'methods' => 'POST',
            'callback' => [$controller, 'login'],
            'permission_callback' => '__return_true'
        ]);

        // POST /auth/refresh-token
        register_rest_route($namespace, '/auth/refresh-token', [
            'methods' => 'POST',
            'callback' => [$controller, 'refreshToken'],
            'permission_callback' => '__return_true'
        ]);

        // GET /auth/me
        register_rest_route($namespace, '/auth/me', [
            'methods' => 'GET',
            'callback' => [$controller, 'me'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /auth/logout
        register_rest_route($namespace, '/auth/logout', [
            'methods' => 'POST',
            'callback' => [$controller, 'logout'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
