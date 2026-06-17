<?php
namespace ConstructionManagementApi\Routes;

use ConstructionManagementApi\Controllers\LabourController;
use ConstructionManagementApi\Middleware\RoleMiddleware;

class LabourRoutes {
    
    public static function register() {
        $controller = new LabourController();
        $namespace = 'construction-management/v1';

        // GET /labours
        register_rest_route($namespace, '/labours', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /labours/:id
        register_rest_route($namespace, '/labours/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /labours
        register_rest_route($namespace, '/labours', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_labour')
        ]);

        // PUT /labours/:id
        register_rest_route($namespace, '/labours/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_labour')
        ]);

        // DELETE /labours/:id
        register_rest_route($namespace, '/labours/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_labour')
        ]);

        // --- ATTENDANCE ---

        // GET /attendance
        register_rest_route($namespace, '/attendance', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAllAttendance'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /attendance
        register_rest_route($namespace, '/attendance', [
            'methods' => 'POST',
            'callback' => [$controller, 'createAttendance'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_labour')
        ]);

        // PUT /attendance/:id
        register_rest_route($namespace, '/attendance/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateAttendance'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_labour')
        ]);

        // DELETE /attendance/:id
        register_rest_route($namespace, '/attendance/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteAttendance'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_labour')
        ]);

        // --- PAYROLL ---

        // GET /payroll
        register_rest_route($namespace, '/payroll', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAllPayroll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /payroll
        register_rest_route($namespace, '/payroll', [
            'methods' => 'POST',
            'callback' => [$controller, 'createPayroll'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_labour')
        ]);

        // PUT /payroll/:id
        register_rest_route($namespace, '/payroll/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updatePayroll'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_labour')
        ]);

        // DELETE /payroll/:id
        register_rest_route($namespace, '/payroll/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deletePayroll'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_labour')
        ]);
    }
}
