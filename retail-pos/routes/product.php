<?php
namespace RetailPosApi\Routes;

use RetailPosApi\Controllers\ProductController;
use RetailPosApi\Middleware\RoleMiddleware;

class ProductRoutes {
    public static function register() {
        $controller = new ProductController();
        $namespace = 'retail-pos/v1';

        register_rest_route($namespace, '/products', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_products')
            ]
        ]);

        register_rest_route($namespace, '/products/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_products')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_products')
            ]
        ]);

        // Barcode scanning / SKU lookup
        register_rest_route($namespace, '/products/barcode/(?P<code>[a-zA-Z0-9\-\*]+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getByBarcode'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // Barcode SVG generation
        register_rest_route($namespace, '/products/barcode/generate', [
            'methods' => 'POST',
            'callback' => [$controller, 'generateBarcode'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
