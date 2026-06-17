<?php
namespace AccountingManagementApi\Routes;

use AccountingManagementApi\Controllers\AccountController;
use AccountingManagementApi\Middleware\RoleMiddleware;

class AccountRoutes {
    
    public static function register() {
        $controller = new AccountController();
        $namespace = 'accounting-management/v1';

        // GET /accounts
        register_rest_route($namespace, '/accounts', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /accounts/:id
        register_rest_route($namespace, '/accounts/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /accounts
        register_rest_route($namespace, '/accounts', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_accounts')
        ]);

        // PUT /accounts/:id
        register_rest_route($namespace, '/accounts/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_accounts')
        ]);

        // DELETE /accounts/:id
        register_rest_route($namespace, '/accounts/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_accounts')
        ]);
    }
}
