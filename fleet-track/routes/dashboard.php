<?php
namespace FleetTrackPro\Routes;

use FleetTrackPro\Controllers\DashboardController;
use FleetTrackPro\Middleware\RoleMiddleware;

class DashboardRoutes {
    
    public static function register() {
        $controller = new DashboardController();
        $namespace = 'fleet-track/v1';

        register_rest_route($namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSummary'],
            'permission_callback' => RoleMiddleware::hasCapability('view_fleet')
        ]);
    }
}
