<?php
namespace FleetTrackPro\Routes;

use FleetTrackPro\Controllers\TripController;
use FleetTrackPro\Middleware\RoleMiddleware;

class TripRoutes {
    
    public static function register() {
        $controller = new TripController();
        $namespace = 'fleet-track/v1';

        register_rest_route($namespace, '/trips', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'index'],
                'permission_callback' => RoleMiddleware::hasCapability('view_trips')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_trips')
            ]
        ]);

        register_rest_route($namespace, '/trips/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'show'],
                'permission_callback' => RoleMiddleware::hasCapability('view_trips')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('update_trip_status')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_trips')
            ]
        ]);
    }
}
