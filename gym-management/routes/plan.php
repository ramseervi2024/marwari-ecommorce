<?php
namespace GymErpApi\Routes;
use GymErpApi\Controllers\PlanController;
use GymErpApi\Middleware\AuthMiddleware;
if (!defined('ABSPATH')) exit;
class PlanRoutes {
    public static function register() {
        $ns = 'gym/v1';
        register_rest_route($ns, '/plans', [
            ['methods' => 'GET', 'callback' => [new PlanController(), 'getPlans'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [new PlanController(), 'createPlan'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]
        ]);
    }
}
