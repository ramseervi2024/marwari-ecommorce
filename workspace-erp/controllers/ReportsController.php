<?php
namespace WorkspaceErpApi\Controllers;

use WP_REST_Request;

class ReportsController extends BaseController {

    public function getRevenueReport(WP_REST_Request $request) {
        return $this->success('Revenue reports fetched', [
            'total_revenue' => 250750.00,
            'billing_month' => date('Y-m'),
            'payments_completed' => 0.00,
            'outstanding' => 250750.00
        ]);
    }

    public function getOccupancyReport(WP_REST_Request $request) {
        return $this->success('Occupancy reports fetched', [
            'total_capacity' => 1800,
            'allocated_seats' => 4,
            'occupancy_percentage' => 66.7
        ]);
    }

    public function getTicketsReport(WP_REST_Request $request) {
        return $this->success('Facility SLA reports fetched', [
            'total_tickets' => 1,
            'resolved_tickets' => 0,
            'sla_compliance_percentage' => 100.0
        ]);
    }

    public function getEsgReport(WP_REST_Request $request) {
        return $this->success('ESG ESG/sustainability report metrics fetched', [
            'carbon_footprint_reduction_kg' => 1250,
            'energy_efficiency_percent' => 88.5,
            'recycling_rate_percent' => 74.0
        ]);
    }
}
