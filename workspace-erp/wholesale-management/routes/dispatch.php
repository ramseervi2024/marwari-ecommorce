<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\DispatchController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class DispatchRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new DispatchController();
        register_rest_route($ns, '/dispatches', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getDispatches'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createDispatch'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/dispatches/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getDispatch'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updateDispatch'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteDispatch'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
