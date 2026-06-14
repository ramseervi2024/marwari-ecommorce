<?php
namespace FleetTrackPro\Routes;

use FleetTrackPro\Controllers\ExpenseController;
use FleetTrackPro\Middleware\RoleMiddleware;

class ExpenseRoutes {
    
    public static function register() {
        $controller = new ExpenseController();
        $namespace = 'fleet-track/v1';

        register_rest_route($namespace, '/expenses', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'index'],
                'permission_callback' => RoleMiddleware::hasCapability('view_expenses')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_expenses')
            ]
        ]);

        register_rest_route($namespace, '/expenses/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'show'],
                'permission_callback' => RoleMiddleware::hasCapability('view_expenses')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_expenses')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_expenses')
            ]
        ]);
    }
}
