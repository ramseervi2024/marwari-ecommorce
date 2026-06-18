<?php
namespace GymErpApi\Routes;
use GymErpApi\Controllers\TrainerController;
use GymErpApi\Middleware\AuthMiddleware;
if (!defined('ABSPATH')) exit;
class TrainerRoutes {
    public static function register() {
        $ns = 'gym/v1';
        register_rest_route($ns, '/trainers', [
            ['methods' => 'GET', 'callback' => [new TrainerController(), 'getTrainers'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [new TrainerController(), 'createTrainer'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]
        ]);
        register_rest_route($ns, '/trainers/(?P<id>\d+)', [
            ['methods' => 'DELETE', 'callback' => [new TrainerController(), 'deleteTrainer'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]
        ]);
    }
}
