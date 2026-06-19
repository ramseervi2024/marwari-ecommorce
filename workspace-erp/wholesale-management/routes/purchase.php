<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\PurchaseController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class PurchaseRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new PurchaseController();
        register_rest_route($ns, '/purchases', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getPurchases'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createPurchase'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/purchases/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getPurchase'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updatePurchase'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deletePurchase'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
