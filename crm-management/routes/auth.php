<?php
namespace CrmManagementApi\Routes;

use CrmManagementApi\Controllers\AuthController;
use CrmManagementApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) {
    exit;
}

class AuthRoutes {
    public static function register() {
        $namespace = 'crm/v1';

        // Public routes (no auth required)
        register_rest_route($namespace, '/auth/register', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'register'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($namespace, '/auth/register/verify', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'verifyRegister'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($namespace, '/auth/login/initiate', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'initiateLogin'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($namespace, '/auth/login', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'login'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($namespace, '/auth/refresh-token', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'refreshToken'],
            'permission_callback' => '__return_true',
        ]);

        // Protected routes (JWT required)
        register_rest_route($namespace, '/auth/me', [
            'methods'             => 'GET',
            'callback'            => [new AuthController(), 'me'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/auth/logout', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'logout'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        // SMTP Settings
        register_rest_route($namespace, '/auth/smtp', [
            [
                'methods'             => 'GET',
                'callback'            => [new AuthController(), 'getSmtpSettings'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [new AuthController(), 'saveSmtpSettings'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        register_rest_route($namespace, '/auth/smtp/test', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'testSmtpSettings'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        // User Management
        register_rest_route($namespace, '/auth/users', [
            'methods'             => 'GET',
            'callback'            => [new AuthController(), 'getUsers'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/auth/users/status', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'updateUserStatus'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/auth/users/(?P<id>\d+)', [
            'methods'             => 'DELETE',
            'callback'            => [new AuthController(), 'deleteUser'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);
    }
}
