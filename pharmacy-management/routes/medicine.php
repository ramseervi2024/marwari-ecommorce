<?php
namespace PharmacyErpApi\Routes;

use PharmacyErpApi\Controllers\MedicineController;
use PharmacyErpApi\Controllers\CategoryController;
use PharmacyErpApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class MedicineRoutes {
    public static function register() {
        $ns = 'pharmacy/v1';
        
        // Medicines
        register_rest_route($ns, '/medicines', [
            [
                'methods' => 'GET', 'callback' => [new MedicineController(), 'getMedicines'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'POST', 'callback' => [new MedicineController(), 'createMedicine'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]
        ]);
        register_rest_route($ns, '/medicines/(?P<id>\d+)', [
            [
                'methods' => 'GET', 'callback' => [new MedicineController(), 'getMedicine'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'PUT', 'callback' => [new MedicineController(), 'updateMedicine'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'DELETE', 'callback' => [new MedicineController(), 'deleteMedicine'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]
        ]);

        // Categories
        register_rest_route($ns, '/categories', [
            [
                'methods' => 'GET', 'callback' => [new CategoryController(), 'getCategories'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'POST', 'callback' => [new CategoryController(), 'createCategory'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]
        ]);
        register_rest_route($ns, '/categories/(?P<id>\d+)', [
            [
                'methods' => 'PUT', 'callback' => [new CategoryController(), 'updateCategory'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ],
            [
                'methods' => 'DELETE', 'callback' => [new CategoryController(), 'deleteCategory'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]
        ]);
    }
}
