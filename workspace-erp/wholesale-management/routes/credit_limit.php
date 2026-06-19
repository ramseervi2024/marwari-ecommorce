<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\CreditLimitController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class CreditLimitRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new CreditLimitController();
        register_rest_route($ns, '/credit-limits', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getCreditLimits'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createCreditLimit'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/credit-limits/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getCreditLimit'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updateCreditLimit'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteCreditLimit'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
