<?php
namespace CrmManagementApi\Routes;

use CrmManagementApi\Controllers\CommunicationController;
use CrmManagementApi\Controllers\ReportController;
use CrmManagementApi\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) {
    exit;
}

class CommunicationRoutes {
    public static function register() {
        $namespace = 'crm/v1';

        // Call Logs
        register_rest_route($namespace, '/call-logs', [
            [
                'methods'             => 'GET',
                'callback'            => [new CommunicationController(), 'getCallLogs'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [new CommunicationController(), 'createCallLog'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        register_rest_route($namespace, '/call-logs/(?P<id>\d+)', [
            [
                'methods'             => 'PUT',
                'callback'            => [new CommunicationController(), 'updateCallLog'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [new CommunicationController(), 'deleteCallLog'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        // Meetings
        register_rest_route($namespace, '/meetings', [
            [
                'methods'             => 'GET',
                'callback'            => [new CommunicationController(), 'getMeetings'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [new CommunicationController(), 'createMeeting'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        register_rest_route($namespace, '/meetings/(?P<id>\d+)', [
            [
                'methods'             => 'PUT',
                'callback'            => [new CommunicationController(), 'updateMeeting'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [new CommunicationController(), 'deleteMeeting'],
                'permission_callback' => [AuthMiddleware::class, 'authenticate'],
            ],
        ]);

        // WhatsApp
        register_rest_route($namespace, '/whatsapp/send', [
            'methods'             => 'POST',
            'callback'            => [new CommunicationController(), 'sendWhatsApp'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/whatsapp/history', [
            'methods'             => 'GET',
            'callback'            => [new CommunicationController(), 'getWhatsAppHistory'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        // Email
        register_rest_route($namespace, '/email/send', [
            'methods'             => 'POST',
            'callback'            => [new CommunicationController(), 'sendEmail'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/email/history', [
            'methods'             => 'GET',
            'callback'            => [new CommunicationController(), 'getEmailHistory'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        // Reports
        register_rest_route($namespace, '/reports/leads', [
            'methods'             => 'GET',
            'callback'            => [new ReportController(), 'getLeadsReport'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/reports/followups', [
            'methods'             => 'GET',
            'callback'            => [new ReportController(), 'getFollowupsReport'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/reports/quotations', [
            'methods'             => 'GET',
            'callback'            => [new ReportController(), 'getQuotationsReport'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/reports/deals', [
            'methods'             => 'GET',
            'callback'            => [new ReportController(), 'getDealsReport'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/reports/pipeline', [
            'methods'             => 'GET',
            'callback'            => [new ReportController(), 'getPipelineReport'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/reports/revenue', [
            'methods'             => 'GET',
            'callback'            => [new ReportController(), 'getRevenueReport'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/reports/team-performance', [
            'methods'             => 'GET',
            'callback'            => [new ReportController(), 'getTeamPerformanceReport'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/reports/lead-sources', [
            'methods'             => 'GET',
            'callback'            => [new ReportController(), 'getLeadSourcesReport'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/reports/conversion-rate', [
            'methods'             => 'GET',
            'callback'            => [new ReportController(), 'getConversionRateReport'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);

        register_rest_route($namespace, '/reports/forecast', [
            'methods'             => 'GET',
            'callback'            => [new ReportController(), 'getForecastReport'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate'],
        ]);
    }
}
