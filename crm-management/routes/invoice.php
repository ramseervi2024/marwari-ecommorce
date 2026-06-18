<?php
namespace CrmManagementApi\Routes;

use CrmManagementApi\Controllers\InvoiceController;
use CrmManagementApi\Controllers\DocumentController;
use CrmManagementApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) {
    exit;
}

class InvoiceRoutes {
    public static function register() {
        $namespace = 'crm/v1';

        // Invoices
        register_rest_route($namespace, '/invoices', [
            [
                'methods'             => 'GET',
                'callback'            => [new InvoiceController(), 'getInvoices'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [new InvoiceController(), 'createInvoice'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        register_rest_route($namespace, '/invoices/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [new InvoiceController(), 'getInvoice'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [new InvoiceController(), 'updateInvoice'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [new InvoiceController(), 'deleteInvoice'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        // Payments
        register_rest_route($namespace, '/payments', [
            [
                'methods'             => 'GET',
                'callback'            => [new InvoiceController(), 'getPayments'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [new InvoiceController(), 'createPayment'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        register_rest_route($namespace, '/payments/(?P<id>\d+)', [
            [
                'methods'             => 'PUT',
                'callback'            => [new InvoiceController(), 'updatePayment'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [new InvoiceController(), 'deletePayment'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        // Documents
        register_rest_route($namespace, '/documents', [
            [
                'methods'             => 'GET',
                'callback'            => [new DocumentController(), 'getDocuments'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [new DocumentController(), 'createDocument'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        register_rest_route($namespace, '/documents/(?P<id>\d+)', [
            [
                'methods'             => 'PUT',
                'callback'            => [new DocumentController(), 'updateDocument'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [new DocumentController(), 'deleteDocument'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        // Media Upload
        register_rest_route($namespace, '/media/upload', [
            'methods'             => 'POST',
            'callback'            => [new DocumentController(), 'uploadMedia'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);
    }
}
