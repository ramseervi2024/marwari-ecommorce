<?php
namespace CustomerManager\Routes;

use CustomerManager\Controllers\CustomerController;
use CustomerManager\Middleware\RoleMiddleware;
use WP_REST_Server;

class CustomerRoutes {
    
    public static function register() {
        $namespace = 'customer-manager/v1';
        $controller = new CustomerController();

        // GET /customers/export (Super Admin only)
        register_rest_route($namespace, '/customers/export', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$controller, 'export'],
            'permission_callback' => RoleMiddleware::hasCapability('export_customers')
        ]);

        // POST /customers/import (Super Admin only)
        register_rest_route($namespace, '/customers/import', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$controller, 'import'],
            'permission_callback' => RoleMiddleware::hasCapability('import_customers')
        ]);

        // GET /customers (Super Admin, Manager, Viewer)
        register_rest_route($namespace, '/customers', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('view_customers')
        ]);

        // GET /customers/{id} (Super Admin, Manager, Viewer)
        register_rest_route($namespace, '/customers/(?P<id>\d+)', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$controller, 'show'],
            'permission_callback' => RoleMiddleware::hasCapability('view_customers'),
            'args' => [
                'id' => [
                    'validate_callback' => fn($param) => is_numeric($param),
                    'required' => true
                ]
            ]
        ]);

        // POST /customers (Super Admin, Manager)
        register_rest_route($namespace, '/customers', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('create_customers')
        ]);

        // PUT /customers/{id} (Super Admin, Manager)
        register_rest_route($namespace, '/customers/(?P<id>\d+)', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('edit_customers'),
            'args' => [
                'id' => [
                    'validate_callback' => fn($param) => is_numeric($param),
                    'required' => true
                ]
            ]
        ]);

        // DELETE /customers/{id} (Super Admin only)
        register_rest_route($namespace, '/customers/(?P<id>\d+)', [
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('delete_customers'),
            'args' => [
                'id' => [
                    'validate_callback' => fn($param) => is_numeric($param),
                    'required' => true
                ]
            ]
        ]);
    }
}
