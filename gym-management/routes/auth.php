<?php
namespace GymErpApi\Routes;
use GymErpApi\Controllers\AuthController;
use GymErpApi\Middleware\AuthMiddleware;
if (!defined('ABSPATH')) exit;
class AuthRoutes {
    public static function register() {
        $ns = 'gym/v1';
        register_rest_route($ns, '/auth/login', ['methods' => 'POST', 'callback' => [new AuthController(), 'login'], 'permission_callback' => '__return_true']);
        register_rest_route($ns, '/auth/me', ['methods' => 'GET', 'callback' => [new AuthController(), 'me'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]);
        register_rest_route($ns, '/auth/logout', ['methods' => 'POST', 'callback' => [new AuthController(), 'logout'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]);
    }
}
