<?php
namespace GymErpApi\Routes;
use GymErpApi\Controllers\DashboardController;
use GymErpApi\Middleware\AuthMiddleware;
if (!defined('ABSPATH')) exit;
class DashboardRoutes {
    public static function register() {
        register_rest_route('gym/v1', '/dashboard/stats', ['methods' => 'GET', 'callback' => [new DashboardController(), 'getStats'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]);
    }
}
