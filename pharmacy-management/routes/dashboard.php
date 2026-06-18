<?php
namespace PharmacyErpApi\Routes;

use PharmacyErpApi\Controllers\DashboardController;
use PharmacyErpApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class DashboardRoutes {
    public static function register() {
        register_rest_route('pharmacy/v1', '/dashboard/stats', [
            'methods' => 'GET', 'callback' => [new DashboardController(), 'getStats'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
    }
}
