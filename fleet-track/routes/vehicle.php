<?php
namespace FleetTrackPro\Routes;

use FleetTrackPro\Controllers\VehicleController;
use FleetTrackPro\Middleware\RoleMiddleware;

class VehicleRoutes {
    
    public static function register() {
        $controller = new VehicleController();
        $namespace = 'fleet-track/v1';

        // CRUD Endpoints
        register_rest_route($namespace, '/vehicles', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'index'],
                'permission_callback' => RoleMiddleware::hasCapability('view_vehicles')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_vehicles')
            ]
        ]);

        register_rest_route($namespace, '/vehicles/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'show'],
                'permission_callback' => RoleMiddleware::hasCapability('view_vehicles')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_vehicles')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_vehicles')
            ]
        ]);
    }
}
