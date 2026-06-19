<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\SupplierController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class SupplierRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new SupplierController();
        register_rest_route($ns, '/suppliers', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getSuppliers'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createSupplier'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/suppliers/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getSupplier'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updateSupplier'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteSupplier'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
