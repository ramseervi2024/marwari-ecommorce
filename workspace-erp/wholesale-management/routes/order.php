<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\OrderController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class OrderRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new OrderController();
        register_rest_route($ns, '/orders', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getOrders'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createOrder'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/orders/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getOrder'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updateOrder'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteOrder'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
