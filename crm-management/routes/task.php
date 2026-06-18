<?php
namespace CrmManagementApi\Routes;

use CrmManagementApi\Controllers\TaskController;
use CrmManagementApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) {
    exit;
}

class TaskRoutes {
    public static function register() {
        $namespace = 'crm/v1';

        register_rest_route($namespace, '/tasks', [
            [
                'methods'             => 'GET',
                'callback'            => [new TaskController(), 'getTasks'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [new TaskController(), 'createTask'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        register_rest_route($namespace, '/tasks/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [new TaskController(), 'getTask'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [new TaskController(), 'updateTask'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [new TaskController(), 'deleteTask'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);
    }
}
