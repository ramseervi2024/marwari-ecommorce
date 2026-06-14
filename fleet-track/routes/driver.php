<?php
namespace FleetTrackPro\Routes;

use FleetTrackPro\Controllers\DriverController;
use FleetTrackPro\Middleware\RoleMiddleware;

class DriverRoutes {
    
    public static function register() {
        $controller = new DriverController();
        $namespace = 'fleet-track/v1';

        register_rest_route($namespace, '/drivers', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'index'],
                'permission_callback' => RoleMiddleware::hasCapability('view_drivers')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_drivers')
            ]
        ]);

        register_rest_route($namespace, '/drivers/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'show'],
                'permission_callback' => RoleMiddleware::hasCapability('view_drivers')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_drivers')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_drivers')
            ]
        ]);
    }
}
