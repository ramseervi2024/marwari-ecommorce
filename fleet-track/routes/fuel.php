<?php
namespace FleetTrackPro\Routes;

use FleetTrackPro\Controllers\FuelController;
use FleetTrackPro\Middleware\RoleMiddleware;

class FuelRoutes {
    
    public static function register() {
        $controller = new FuelController();
        $namespace = 'fleet-track/v1';

        register_rest_route($namespace, '/fuel', [
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

        register_rest_route($namespace, '/fuel/(?P<id>\d+)', [
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
