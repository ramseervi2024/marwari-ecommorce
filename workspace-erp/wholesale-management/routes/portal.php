<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\PortalController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class PortalRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new PortalController();

        register_rest_route($ns, '/portal/dashboard', [
            'methods' => 'GET',
            'callback' => [$ctrl, 'getDashboard'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
        register_rest_route($ns, '/portal/orders', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getOrders'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createOrder'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/portal/payments', [
            'methods' => 'GET',
            'callback' => [$ctrl, 'getPayments'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
        register_rest_route($ns, '/portal/invoices', [
            'methods' => 'GET',
            'callback' => [$ctrl, 'getInvoices'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
    }
}
