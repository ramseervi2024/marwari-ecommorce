<?php
namespace ServiceManagementApi\Controllers;

use WP_REST_Response;

class BaseController {
    protected function success(string $message = '', $data = [], int $status_code = 200): WP_REST_Response {
        return new WP_REST_Response([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }

    protected function error(string $message = '', $data = [], int $status_code = 400): WP_REST_Response {
        return new WP_REST_Response([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }
}
