<?php
namespace CrmManagementApi\Routes;

use CrmManagementApi\Controllers\DealController;
use CrmManagementApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) {
    exit;
}

class DealRoutes {
    public static function register() {
        $namespace = 'crm/v1';

        register_rest_route($namespace, '/deals', [
            [
                'methods'             => 'GET',
                'callback'            => [new DealController(), 'getDeals'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [new DealController(), 'createDeal'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        register_rest_route($namespace, '/deals/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [new DealController(), 'getDeal'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [new DealController(), 'updateDeal'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [new DealController(), 'deleteDeal'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        // Pipeline (Kanban)
        register_rest_route($namespace, '/pipeline', [
            'methods'             => 'GET',
            'callback'            => [new DealController(), 'getPipeline'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/pipeline/(?P<id>\d+)', [
            'methods'             => 'PUT',
            'callback'            => [new DealController(), 'updateDeal'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);
    }
}
