<?php
namespace GymErpApi\Routes;
use GymErpApi\Controllers\EquipmentController;
use GymErpApi\Middleware\AuthMiddleware;
if (!defined('ABSPATH')) exit;
class EquipmentRoutes {
    public static function register() {
        $ns = 'gym/v1';
        $ctrl = new EquipmentController();
        $auth = [AuthMiddleware::class, 'authenticate'];
        register_rest_route($ns, '/equipment', [
            ['methods' => 'GET',  'callback' => [$ctrl, 'getEquipment'],    'permission_callback' => $auth],
            ['methods' => 'POST', 'callback' => [$ctrl, 'createEquipment'], 'permission_callback' => $auth]
        ]);
        register_rest_route($ns, '/equipment/summary', [
            ['methods' => 'GET', 'callback' => [$ctrl, 'getSummary'], 'permission_callback' => $auth]
        ]);
        register_rest_route($ns, '/equipment/(?P<id>\d+)', [
            ['methods' => 'GET',    'callback' => [$ctrl, 'getEquipmentItem'],  'permission_callback' => $auth],
            ['methods' => 'PUT',    'callback' => [$ctrl, 'updateEquipment'],   'permission_callback' => $auth],
            ['methods' => 'DELETE', 'callback' => [$ctrl, 'deleteEquipment'],   'permission_callback' => $auth]
        ]);
        register_rest_route($ns, '/equipment/(?P<id>\d+)/maintenance', [
            ['methods' => 'POST', 'callback' => [$ctrl, 'logMaintenance'], 'permission_callback' => $auth]
        ]);
    }
}
