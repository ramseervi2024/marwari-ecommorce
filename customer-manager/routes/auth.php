<?php
namespace CustomerManager\Routes;

use CustomerManager\Controllers\AuthController;
use CustomerManager\Middleware\AuthMiddleware;
use WP_REST_Server;

class AuthRoutes {
    
    public static function register() {
        $namespace = 'customer-manager/v1';
        $controller = new AuthController();

        // POST /auth/register
        register_rest_route($namespace, '/auth/register', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$controller, 'register'],
            'permission_callback' => '__return_true' // Public registration endpoint
        ]);

        // POST /auth/login
        register_rest_route($namespace, '/auth/login', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$controller, 'login'],
            'permission_callback' => '__return_true' // Public credentials verification
        ]);

        // POST /auth/logout
        register_rest_route($namespace, '/auth/logout', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$controller, 'logout'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);

        // POST /auth/refresh-token
        register_rest_route($namespace, '/auth/refresh-token', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$controller, 'refreshToken'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);

        // GET /auth/me
        register_rest_route($namespace, '/auth/me', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$controller, 'me'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
    }
}
