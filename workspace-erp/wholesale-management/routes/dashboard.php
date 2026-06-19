<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\DashboardController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class DashboardRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new DashboardController();
        register_rest_route($ns, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$ctrl, 'getStats'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
    }
}
