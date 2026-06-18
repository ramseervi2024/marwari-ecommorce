<?php
namespace GymErpApi\Routes;
use GymErpApi\Controllers\PaymentController;
use GymErpApi\Middleware\AuthMiddleware;
if (!defined('ABSPATH')) exit;
class PaymentRoutes {
    public static function register() {
        $ns = 'gym/v1';
        register_rest_route($ns, '/payments', [
            ['methods' => 'GET', 'callback' => [new PaymentController(), 'getPayments'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [new PaymentController(), 'recordPayment'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]
        ]);
    }
}
