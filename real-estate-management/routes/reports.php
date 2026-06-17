<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\ReportsController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class ReportsRoutes {
    
    public static function register() {
        $controller = new ReportsController();
        $namespace = 'real-estate-management/v1';

        // GET /reports/leads
        register_rest_route($namespace, '/reports/leads', [
            'methods' => 'GET',
            'callback' => [$controller, 'getLeadsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/site-visits
        register_rest_route($namespace, '/reports/site-visits', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSiteVisitsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/bookings
        register_rest_route($namespace, '/reports/bookings', [
            'methods' => 'GET',
            'callback' => [$controller, 'getBookingsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/payments
        register_rest_route($namespace, '/reports/payments', [
            'methods' => 'GET',
            'callback' => [$controller, 'getPaymentsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/collections
        register_rest_route($namespace, '/reports/collections', [
            'methods' => 'GET',
            'callback' => [$controller, 'getCollectionsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/commissions
        register_rest_route($namespace, '/reports/commissions', [
            'methods' => 'GET',
            'callback' => [$controller, 'getCommissionsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/projects
        register_rest_route($namespace, '/reports/projects', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProjectsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/sales
        register_rest_route($namespace, '/reports/sales', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSalesReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/profit-loss
        register_rest_route($namespace, '/reports/profit-loss', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProfitLossReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
    }
}
