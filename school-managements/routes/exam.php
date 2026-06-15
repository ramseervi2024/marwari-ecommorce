<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\ExamController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class ExamRoutes {
    
    public static function register() {
        $controller = new ExamController();
        $namespace = 'school-management/v1';

        // Exams
        register_rest_route($namespace, '/exams', [
            'methods' => 'GET',
            'callback' => [$controller, 'getExams'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/exams', [
            'methods' => 'POST',
            'callback' => [$controller, 'createExam'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_exams')
        ]);
        register_rest_route($namespace, '/exams/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateExam'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_exams')
        ]);
        register_rest_route($namespace, '/exams/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteExam'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_exams')
        ]);

        // Marks Entry
        register_rest_route($namespace, '/marks', [
            'methods' => 'POST',
            'callback' => [$controller, 'enterMarks'],
            'permission_callback' => RoleMiddleware::hasCapability('enter_marks')
        ]);
        register_rest_route($namespace, '/marks/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateMarks'],
            'permission_callback' => RoleMiddleware::hasCapability('enter_marks')
        ]);
        register_rest_route($namespace, '/marks/student/(?P<studentId>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getStudentMarks'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // Report Cards
        register_rest_route($namespace, '/report-cards', [
            'methods' => 'GET',
            'callback' => [$controller, 'getReportCards'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/report-cards/(?P<studentId>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getStudentReportCard'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
