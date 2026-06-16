<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\PatientController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class MedicalRoutes {
    public static function register() {
        $controller = new PatientController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/medical-records', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getMedicalRecords'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ]
        ]);
    }
}
