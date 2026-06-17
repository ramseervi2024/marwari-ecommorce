<?php
namespace TransportManagementApi\Routes;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Controllers\SalaryController;
use TransportManagementApi\Middleware\RoleMiddleware;

class SalaryRoutes {
    
    public static function register() {
        $controller = new SalaryController();
        $namespace = 'transport-management/v1';

        register_rest_route($namespace, '/driver-salary', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/driver-salary/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/driver-salary', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_salaries')
        ]);

        register_rest_route($namespace, '/driver-salary/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_salaries')
        ]);

        register_rest_route($namespace, '/driver-salary/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_salaries')
        ]);
    }
}
