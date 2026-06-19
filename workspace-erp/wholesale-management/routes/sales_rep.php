<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\SalesRepController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class SalesRepRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new SalesRepController();
        register_rest_route($ns, '/sales-reps', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getSalesReps'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createSalesRep'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/sales-reps/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getSalesRep'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updateSalesRep'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteSalesRep'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
