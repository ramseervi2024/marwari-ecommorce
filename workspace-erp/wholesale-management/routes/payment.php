<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\PaymentController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class PaymentRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new PaymentController();
        register_rest_route($ns, '/payments', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getPayments'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createPayment'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/payments/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getPayment'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updatePayment'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deletePayment'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
