<?php
namespace PharmacyErpApi\Routes;

use PharmacyErpApi\Controllers\AuthController;
use PharmacyErpApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class AuthRoutes {
    public static function register() {
        $ns = 'pharmacy/v1';
        register_rest_route($ns, '/auth/login', [
            'methods' => 'POST', 'callback' => [new AuthController(), 'login'], 'permission_callback' => '__return_true'
        ]);
        register_rest_route($ns, '/auth/me', [
            'methods' => 'GET', 'callback' => [new AuthController(), 'me'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
        register_rest_route($ns, '/auth/logout', [
            'methods' => 'POST', 'callback' => [new AuthController(), 'logout'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
        register_rest_route($ns, '/users', [
            'methods' => 'GET', 'callback' => [new AuthController(), 'getUsers'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
        register_rest_route($ns, '/users/status', [
            'methods' => 'PUT', 'callback' => [new AuthController(), 'updateUserStatus'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
        register_rest_route($ns, '/settings/smtp', [
            [
                'methods' => 'GET', 'callback' => [new AuthController(), 'getSmtpSettings'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'PUT', 'callback' => [new AuthController(), 'saveSmtpSettings'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]
        ]);
    }
}
