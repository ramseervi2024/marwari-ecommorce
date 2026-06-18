<?php
namespace PharmacyErpApi\Routes;

use PharmacyErpApi\Controllers\PurchaseController;
use PharmacyErpApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class PurchaseRoutes {
    public static function register() {
        $ns = 'pharmacy/v1';
        register_rest_route($ns, '/purchases', [
            [
                'methods' => 'GET', 'callback' => [new PurchaseController(), 'getPurchases'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'POST', 'callback' => [new PurchaseController(), 'createPurchase'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]
        ]);
        register_rest_route($ns, '/purchases/(?P<id>\d+)', [
            [
                'methods' => 'GET', 'callback' => [new PurchaseController(), 'getPurchase'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'DELETE', 'callback' => [new PurchaseController(), 'deletePurchase'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]
        ]);
    }
}
