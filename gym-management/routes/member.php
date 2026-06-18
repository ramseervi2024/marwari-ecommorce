<?php
namespace GymErpApi\Routes;
use GymErpApi\Controllers\MemberController;
use GymErpApi\Middleware\AuthMiddleware;
if (!defined('ABSPATH')) exit;
class MemberRoutes {
    public static function register() {
        $ns = 'gym/v1';
        register_rest_route($ns, '/members', [
            ['methods' => 'GET', 'callback' => [new MemberController(), 'getMembers'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'POST', 'callback' => [new MemberController(), 'createMember'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]
        ]);
        register_rest_route($ns, '/members/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [new MemberController(), 'getMember'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'PUT', 'callback' => [new MemberController(), 'updateMember'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']],
            ['methods' => 'DELETE', 'callback' => [new MemberController(), 'deleteMember'], 'permission_callback' => [AuthMiddleware::class, 'authenticate']]
        ]);
    }
}
