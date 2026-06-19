<?php
namespace WorkspaceErpApi\Controllers;

use WP_REST_Request;

class AnalyticsController extends BaseController {

    public function getUtilization(WP_REST_Request $request) {
        return $this->success('Workspace utilization analysis', [
            'peak_hours' => '11:00 - 16:00',
            'average_meeting_duration_mins' => 45,
            'hotdesk_turnover_rate' => 1.8
        ]);
    }

    public function getSlaCompliance(WP_REST_Request $request) {
        return $this->success('Operational SLA compliance metrics', [
            'priority_high_compliance_percent' => 95.0,
            'priority_medium_compliance_percent' => 98.2,
            'average_resolution_time_hrs' => 3.5
        ]);
    }
}
