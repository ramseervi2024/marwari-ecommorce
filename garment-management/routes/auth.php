<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\AuthController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class AuthRoutes {
    public static function register() {
        $controller = new AuthController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/auth/register', [
            'methods' => 'POST',
            'callback' => [$controller, 'register'],
            'permission_callback' => '__return_true'
        ]);
        register_rest_route($namespace, '/auth/register/verify', [
            'methods' => 'POST',
            'callback' => [$controller, 'verifyRegister'],
            'permission_callback' => '__return_true'
        ]);
        register_rest_route($namespace, '/auth/login/initiate', [
            'methods' => 'POST',
            'callback' => [$controller, 'initiateLogin'],
            'permission_callback' => '__return_true'
        ]);
        register_rest_route($namespace, '/auth/login', [
            'methods' => 'POST',
            'callback' => [$controller, 'login'],
            'permission_callback' => '__return_true'
        ]);
        register_rest_route($namespace, '/auth/refresh-token', [
            'methods' => 'POST',
            'callback' => [$controller, 'refreshToken'],
            'permission_callback' => '__return_true'
        ]);

        // Protected
        register_rest_route($namespace, '/auth/me', [
            'methods' => 'GET',
            'callback' => [$controller, 'me'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/auth/logout', [
            'methods' => 'POST',
            'callback' => [$controller, 'logout'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/auth/users', [
            'methods' => 'GET',
            'callback' => [$controller, 'getUsers'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_users')
        ]);
        register_rest_route($namespace, '/auth/users/status', [
            'methods' => 'POST',
            'callback' => [$controller, 'updateUserStatus'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_users')
        ]);
        register_rest_route($namespace, '/auth/users/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteUser'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_users')
        ]);
        register_rest_route($namespace, '/auth/smtp', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSmtpSettings'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_users')
        ]);
        register_rest_route($namespace, '/auth/smtp', [
            'methods' => 'POST',
            'callback' => [$controller, 'saveSmtpSettings'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_users')
        ]);
        register_rest_route($namespace, '/auth/smtp/test', [
            'methods' => 'POST',
            'callback' => [$controller, 'testSmtpSettings'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_users')
        ]);
    }
}
