<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\InventoryController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class InventoryRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new InventoryController();
        register_rest_route($ns, '/inventory', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getInventory'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createInventory'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/inventory/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getInventoryItem'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updateInventory'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteInventory'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
