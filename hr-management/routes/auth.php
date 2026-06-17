<?php
namespace HrManagementApi\Routes;

use HrManagementApi\Controllers\AuthController;
use HrManagementApi\Middleware\AuthMiddleware;

class AuthRoutes {
    public static function register() {
        $namespace = 'hr-management/v1';

        // Public
        register_rest_route($namespace, '/auth/login', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'login'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($namespace, '/auth/register', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'register'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($namespace, '/auth/refresh', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'refresh'],
            'permission_callback' => '__return_true',
        ]);

        // Protected
        register_rest_route($namespace, '/auth/me', [
            'methods'             => 'GET',
            'callback'            => [new AuthController(), 'me'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/auth/change-password', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'changePassword'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/auth/logout', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'logout'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/auth/activity-logs', [
            'methods'             => 'GET',
            'callback'            => [new AuthController(), 'activityLogs'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/auth/smtp-settings', [
            'methods'             => 'POST',
            'callback'            => [new AuthController(), 'updateSmtpSettings'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);
    }
}
