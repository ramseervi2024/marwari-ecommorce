<?php
namespace CrmManagementApi\Routes;

use CrmManagementApi\Controllers\QuotationController;
use CrmManagementApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) {
    exit;
}

class QuotationRoutes {
    public static function register() {
        $namespace = 'crm/v1';

        register_rest_route($namespace, '/quotations', [
            [
                'methods'             => 'GET',
                'callback'            => [new QuotationController(), 'getQuotations'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [new QuotationController(), 'createQuotation'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        register_rest_route($namespace, '/quotations/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [new QuotationController(), 'getQuotation'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [new QuotationController(), 'updateQuotation'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [new QuotationController(), 'deleteQuotation'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);
    }
}
