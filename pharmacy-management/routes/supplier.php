<?php
namespace PharmacyErpApi\Routes;

use PharmacyErpApi\Controllers\SupplierController;
use PharmacyErpApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class SupplierRoutes {
    public static function register() {
        $ns = 'pharmacy/v1';
        register_rest_route($ns, '/suppliers', [
            [
                'methods' => 'GET', 'callback' => [new SupplierController(), 'getSuppliers'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'POST', 'callback' => [new SupplierController(), 'createSupplier'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]
        ]);
        register_rest_route($ns, '/suppliers/(?P<id>\d+)', [
            [
                'methods' => 'GET', 'callback' => [new SupplierController(), 'getSupplier'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'PUT', 'callback' => [new SupplierController(), 'updateSupplier'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'DELETE', 'callback' => [new SupplierController(), 'deleteSupplier'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]
        ]);
    }
}
