<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\AuthController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class AuthRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new AuthController();
        register_rest_route($ns, '/auth/login', [
            'methods' => 'POST',
            'callback' => [$ctrl, 'login'],
            'permission_callback' => '__return_true'
        ]);
        register_rest_route($ns, '/auth/register', [
            'methods' => 'POST',
            'callback' => [$ctrl, 'register'],
            'permission_callback' => '__return_true'
        ]);
        register_rest_route($ns, '/auth/logout', [
            'methods' => 'POST',
            'callback' => [$ctrl, 'logout'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
        register_rest_route($ns, '/auth/refresh-token', [
            'methods' => 'POST',
            'callback' => [$ctrl, 'refreshToken'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
        register_rest_route($ns, '/auth/me', [
            'methods' => 'GET',
            'callback' => [$ctrl, 'me'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
    }
}
