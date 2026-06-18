<?php
namespace CrmManagementApi\Routes;

use CrmManagementApi\Controllers\FollowupController;
use CrmManagementApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) {
    exit;
}

class FollowupRoutes {
    public static function register() {
        $namespace = 'crm/v1';

        register_rest_route($namespace, '/followups', [
            [
                'methods'             => 'GET',
                'callback'            => [new FollowupController(), 'getFollowups'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [new FollowupController(), 'createFollowup'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        register_rest_route($namespace, '/followups/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [new FollowupController(), 'getFollowup'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [new FollowupController(), 'updateFollowup'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [new FollowupController(), 'deleteFollowup'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);
    }
}
