<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\ReportsController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class ReportsRoutes {
    public static function register() {
        $controller = new ReportsController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/reports/sales', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSalesReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/menu-items', [
            'methods' => 'GET',
            'callback' => [$controller, 'getMenuItemsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/inventory', [
            'methods' => 'GET',
            'callback' => [$controller, 'getInventoryReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/profit-loss', [
            'methods' => 'GET',
            'callback' => [$controller, 'getProfitLossReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
    }
}
