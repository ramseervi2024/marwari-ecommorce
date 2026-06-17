<?php
namespace TransportManagementApi\Routes;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Controllers\ReportsController;
use TransportManagementApi\Middleware\RoleMiddleware;

class ReportsRoutes {
    
    public static function register() {
        $controller = new ReportsController();
        $namespace = 'transport-management/v1';

        register_rest_route($namespace, '/reports/trips', [
            'methods' => 'GET',
            'callback' => [$controller, 'getTripsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/reports/fuel', [
            'methods' => 'GET',
            'callback' => [$controller, 'getFuelReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/reports/maintenance', [
            'methods' => 'GET',
            'callback' => [$controller, 'getMaintenanceReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/reports/challans', [
            'methods' => 'GET',
            'callback' => [$controller, 'getChallansReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/reports/drivers', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDriversReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/reports/deliveries', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDeliveriesReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/reports/fleet', [
            'methods' => 'GET',
            'callback' => [$controller, 'getFleetReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/reports/profit-loss', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProfitLossReport'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
