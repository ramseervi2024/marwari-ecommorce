<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\DealerController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class DealerRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new DealerController();
        register_rest_route($ns, '/dealers', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getDealers'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createDealer'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/dealers/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getDealer'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updateDealer'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteDealer'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
