<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\AnalyticsController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class AnalyticsRoutes {
    
    public static function register() {
        $controller = new AnalyticsController();
        $namespace = 'school-management/v1';

        register_rest_route($namespace, '/analytics', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAnalytics'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
    }
}
