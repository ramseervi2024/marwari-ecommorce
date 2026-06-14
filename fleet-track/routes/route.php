<?php
namespace FleetTrackPro\Routes;

use FleetTrackPro\Controllers\RouteController;
use FleetTrackPro\Middleware\RoleMiddleware;

class RouteRoutes {
    
    public static function register() {
        $controller = new RouteController();
        $namespace = 'fleet-track/v1';

        register_rest_route($namespace, '/routes', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'index'],
                'permission_callback' => RoleMiddleware::hasCapability('view_routes')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_routes')
            ]
        ]);

        register_rest_route($namespace, '/routes/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'show'],
                'permission_callback' => RoleMiddleware::hasCapability('view_routes')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_routes')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_routes')
            ]
        ]);
    }
}
