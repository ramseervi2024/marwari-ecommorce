<?php
namespace PharmacyErpApi\Routes;

use PharmacyErpApi\Controllers\BillController;
use PharmacyErpApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class BillRoutes {
    public static function register() {
        $ns = 'pharmacy/v1';
        register_rest_route($ns, '/bills', [
            [
                'methods' => 'GET', 'callback' => [new BillController(), 'getBills'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'POST', 'callback' => [new BillController(), 'createBill'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]
        ]);
        register_rest_route($ns, '/bills/(?P<id>\d+)', [
            [
                'methods' => 'GET', 'callback' => [new BillController(), 'getBill'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'DELETE', 'callback' => [new BillController(), 'deleteBill'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]
        ]);
    }
}
