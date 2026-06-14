<?php
namespace FleetTrackPro\Routes;

use FleetTrackPro\Controllers\ReportController;
use FleetTrackPro\Middleware\RoleMiddleware;

class ReportRoutes {
    
    public static function register() {
        $controller = new ReportController();
        $namespace = 'fleet-track/v1';

        $endpoints = [
            'profit-loss' => 'getProfitLoss',
            'revenue' => 'getRevenue',
            'expenses' => 'getExpenses',
            'fuel' => 'getFuel',
            'vehicle' => 'getVehicleReport',
            'driver' => 'getDriverReport',
            'trips' => 'getTrips'
        ];

        foreach ($endpoints as $path => $method) {
            register_rest_route($namespace, "/reports/$path", [
                'methods' => 'GET',
                'callback' => [$controller, $method],
                'permission_callback' => RoleMiddleware::hasCapability('view_reports')
            ]);
        }
    }
}
