<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\ReportsController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class ReportsRoutes {
    public static function register() {
        $controller = new ReportsController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/reports/revenue', [
            'methods' => 'GET',
            'callback' => [$controller, 'getRevenueReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/patients', [
            'methods' => 'GET',
            'callback' => [$controller, 'getPatientsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/doctors', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDoctorsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/pharmacy', [
            'methods' => 'GET',
            'callback' => [$controller, 'getPharmacyReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/laboratory', [
            'methods' => 'GET',
            'callback' => [$controller, 'getLaboratoryReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/appointments', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAppointmentsReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/billing', [
            'methods' => 'GET',
            'callback' => [$controller, 'getBillingReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/opd', [
            'methods' => 'GET',
            'callback' => [$controller, 'getOpdReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
        register_rest_route($namespace, '/reports/ipd', [
            'methods' => 'GET',
            'callback' => [$controller, 'getIpdReport'],
            'permission_callback' => RoleMiddleware::hasCapability('view_reports')
        ]);
    }
}
