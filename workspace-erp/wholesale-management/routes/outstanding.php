<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\OutstandingController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class OutstandingRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new OutstandingController();
        register_rest_route($ns, '/outstandings', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getOutstandings'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createOutstanding'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/outstandings/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getOutstanding'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updateOutstanding'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteOutstanding'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
