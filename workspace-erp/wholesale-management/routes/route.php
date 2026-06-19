<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\RouteController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class RouteRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new RouteController();
        register_rest_route($ns, '/routes', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getRoutes'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createRoute'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/routes/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getRoute'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updateRoute'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteRoute'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
