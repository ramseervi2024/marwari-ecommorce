<?php
namespace CustomerManager\Controllers;

use CustomerManager\Repositories\CustomerRepository;
use WP_REST_Request;
use WP_REST_Response;

class DashboardController {
    
    private CustomerRepository $repository;

    public function __construct() {
        $this->repository = new CustomerRepository();
    }

    /**
     * Retrieve telemetry stats for the dashboard.
     */
    public function stats(WP_REST_Request $request): WP_REST_Response {
        $stats = $this->repository->getStats();

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Telemetry stats retrieved successfully',
            'data' => $stats
        ], 200);
    }
}
