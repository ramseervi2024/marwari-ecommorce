<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\ExpenseController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class ExpenseRoutes {
    public static function register() {
        $controller = new ExpenseController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/expense', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/expense', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_reports')
        ]);

        register_rest_route($namespace, '/expense/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/expense/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_reports')
        ]);

        register_rest_route($namespace, '/expense/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_reports')
        ]);
    }
}
