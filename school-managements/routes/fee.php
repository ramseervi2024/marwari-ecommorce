<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\FeeController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class FeeRoutes {
    
    public static function register() {
        $controller = new FeeController();
        $namespace = 'school-management/v1';

        // Fee Structures
        register_rest_route($namespace, '/fees/structures', [
            'methods' => 'GET',
            'callback' => [$controller, 'getStructures'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/fees/structures', [
            'methods' => 'POST',
            'callback' => [$controller, 'createStructure'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_fees')
        ]);
        register_rest_route($namespace, '/fees/structures/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateStructure'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_fees')
        ]);
        register_rest_route($namespace, '/fees/structures/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteStructure'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_fees')
        ]);

        // Fee Collection
        register_rest_route($namespace, '/fees/collections', [
            'methods' => 'GET',
            'callback' => [$controller, 'getCollections'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/fees/collections', [
            'methods' => 'POST',
            'callback' => [$controller, 'collectFee'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_fees')
        ]);

        // Fee Reports
        register_rest_route($namespace, '/reports/fees', [
            'methods' => 'GET',
            'callback' => [$controller, 'getFeeReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_financial_reports')
        ]);
    }
}
