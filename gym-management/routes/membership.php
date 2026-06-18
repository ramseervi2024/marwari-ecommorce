<?php
namespace GymErpApi\Routes;
use GymErpApi\Controllers\MembershipController;
use GymErpApi\Middleware\AuthMiddleware;
if (!defined('ABSPATH')) exit;
class MembershipRoutes {
    public static function register() {
        $ns = 'gym/v1';
        register_rest_route($ns, '/memberships', [
            ['methods' => 'GET', 'callback' => [new MembershipController(), 'getMemberships'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [new MembershipController(), 'assignPlan'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]
        ]);
        register_rest_route($ns, '/memberships/expiring', [
            ['methods' => 'GET', 'callback' => [new MembershipController(), 'getExpiring'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]
        ]);
    }
}
