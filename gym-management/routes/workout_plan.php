<?php
namespace GymErpApi\Routes;
use GymErpApi\Controllers\WorkoutPlanController;
use GymErpApi\Middleware\AuthMiddleware;
if (!defined('ABSPATH')) exit;
class WorkoutPlanRoutes {
    public static function register() {
        $ns = 'gym/v1';
        $ctrl = new WorkoutPlanController();
        $auth = [AuthMiddleware::class, 'authenticate'];
        register_rest_route($ns, '/workout-plans', [
            ['methods' => 'GET',  'callback' => [$ctrl, 'getWorkoutPlans'],    'permission_callback' => $auth],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createWorkoutPlan'],  'permission_callback' => $auth]
        ]);
        register_rest_route($ns, '/workout-plans/(?P<id>\d+)', [
            ['methods' => 'GET',    'callback' => [$ctrl, 'getWorkoutPlan'],    'permission_callback' => $auth],
            ['methods' => 'PUT',    'callback' => [$ctrl, 'updateWorkoutPlan'], 'permission_callback' => $auth],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteWorkoutPlan'], 'permission_callback' => $auth]
        ]);
    }
}
