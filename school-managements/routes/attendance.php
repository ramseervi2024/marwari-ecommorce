<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\AttendanceController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class AttendanceRoutes {
    
    public static function register() {
        $controller = new AttendanceController();
        $namespace = 'school-management/v1';

        // Student Attendance
        register_rest_route($namespace, '/attendance/students', [
            'methods' => 'GET',
            'callback' => [$controller, 'getStudentAttendance'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/attendance/students', [
            'methods' => 'POST',
            'callback' => [$controller, 'submitStudentAttendance'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_attendance')
        ]);
        register_rest_route($namespace, '/attendance/students/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateStudentAttendance'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_attendance')
        ]);

        // Teacher Attendance
        register_rest_route($namespace, '/attendance/teachers', [
            'methods' => 'GET',
            'callback' => [$controller, 'getTeacherAttendance'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);
        register_rest_route($namespace, '/attendance/teachers', [
            'methods' => 'POST',
            'callback' => [$controller, 'submitTeacherAttendance'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);
        register_rest_route($namespace, '/attendance/teachers/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateTeacherAttendance'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);
    }
}
