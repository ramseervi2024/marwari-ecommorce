<?php
namespace CrmManagementApi\Routes;

use CrmManagementApi\Controllers\LeadController;
use CrmManagementApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) {
    exit;
}

class LeadRoutes {
    public static function register() {
        $namespace = 'crm/v1';

        register_rest_route($namespace, '/leads', [
            [
                'methods'             => 'GET',
                'callback'            => [new LeadController(), 'getLeads'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [new LeadController(), 'createLead'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        register_rest_route($namespace, '/leads/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [new LeadController(), 'getLead'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [new LeadController(), 'updateLead'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [new LeadController(), 'deleteLead'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);
    }
}
