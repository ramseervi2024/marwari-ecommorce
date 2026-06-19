<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\BillingController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class BillingRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new BillingController();
        register_rest_route($ns, '/billing', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getBillings'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createBilling'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/billing/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getBilling'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updateBilling'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteBilling'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
