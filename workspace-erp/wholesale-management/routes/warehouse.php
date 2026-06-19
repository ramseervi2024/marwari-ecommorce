<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\WarehouseController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class WarehouseRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new WarehouseController();
        register_rest_route($ns, '/warehouses', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getWarehouses'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createWarehouse'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/warehouses/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getWarehouse'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updateWarehouse'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteWarehouse'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
