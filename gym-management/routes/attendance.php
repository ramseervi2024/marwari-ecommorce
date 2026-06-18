<?php
namespace GymErpApi\Routes;
use GymErpApi\Controllers\AttendanceController;
use GymErpApi\Middleware\AuthMiddleware;
if (!defined('ABSPATH')) exit;
class AttendanceRoutes {
    public static function register() {
        $ns = 'gym/v1';
        register_rest_route($ns, '/attendance', [
            ['methods' => 'GET', 'callback' => [new AttendanceController(), 'getAttendance'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [new AttendanceController(), 'markAttendance'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]
        ]);
    }
}
