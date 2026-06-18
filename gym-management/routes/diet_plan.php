<?php
namespace GymErpApi\Routes;
use GymErpApi\Controllers\DietPlanController;
use GymErpApi\Middleware\AuthMiddleware;
if (!defined('ABSPATH')) exit;
class DietPlanRoutes {
    public static function register() {
        $ns = 'gym/v1';
        register_rest_route($ns, '/diet-plans', [
            ['methods' => 'GET', 'callback' => [new DietPlanController(), 'getDietPlans'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [new DietPlanController(), 'assignDietPlan'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]
        ]);
    }
}
