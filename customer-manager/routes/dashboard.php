<?php
namespace CustomerManager\Routes;

use CustomerManager\Controllers\DashboardController;
use CustomerManager\Middleware\RoleMiddleware;
use WP_REST_Server;

class DashboardRoutes {
    
    public static function register() {
        $namespace = 'customer-manager/v1';
        $controller = new DashboardController();

        // GET /dashboard/stats
        register_rest_route($namespace, '/dashboard/stats', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$controller, 'stats'],
            'permission_callback' => RoleMiddleware::hasCapability('access_dashboard')
        ]);
    }
}
