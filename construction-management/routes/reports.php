<?php
namespace ConstructionManagementApi\Routes;

use ConstructionManagementApi\Controllers\ReportsController;
use ConstructionManagementApi\Middleware\RoleMiddleware;

class ReportsRoutes {
    
    public static function register() {
        $controller = new ReportsController();
        $namespace = 'construction-management/v1';

        // GET /reports/project-cost
        register_rest_route($namespace, '/reports/project-cost', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProjectCost'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/profitability
        register_rest_route($namespace, '/reports/profitability', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProfitability'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);

        // GET /reports/budget-vs-actual
        register_rest_route($namespace, '/reports/budget-vs-actual', [
            'methods' => 'GET',
            'callback' => [$controller, 'getBudgetVsActual'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
    }
}
