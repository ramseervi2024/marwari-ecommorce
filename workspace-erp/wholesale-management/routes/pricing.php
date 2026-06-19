<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\PricingController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class PricingRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new PricingController();
        register_rest_route($ns, '/pricing', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getPricings'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createPricing'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/pricing/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getPricing'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updatePricing'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deletePricing'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
