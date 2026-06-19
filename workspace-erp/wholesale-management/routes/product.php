<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\ProductController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class ProductRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new ProductController();
        register_rest_route($ns, '/products', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getProducts'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createProduct'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
        register_rest_route($ns, '/products/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getProduct'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [$ctrl, 'updateProduct'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteProduct'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
        ]);
    }
}
