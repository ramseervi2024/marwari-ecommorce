<?php
namespace PharmacyErpApi\Routes;

use PharmacyErpApi\Controllers\BatchController;
use PharmacyErpApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class BatchRoutes {
    public static function register() {
        $ns = 'pharmacy/v1';
        
        register_rest_route($ns, '/batches', [
            'methods' => 'GET', 'callback' => [new BatchController(), 'getBatches'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
        
        register_rest_route($ns, '/batches/(?P<id>\d+)', [
            [
                'methods' => 'PUT', 'callback' => [new BatchController(), 'updateBatch'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'DELETE', 'callback' => [new BatchController(), 'deleteBatch'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]
        ]);
        
        register_rest_route($ns, '/batches/alerts', [
            'methods' => 'GET', 'callback' => [new BatchController(), 'getExpiryAlerts'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
        
        register_rest_route($ns, '/batches/expired', [
            'methods' => 'GET', 'callback' => [new BatchController(), 'getExpired'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
    }
}
