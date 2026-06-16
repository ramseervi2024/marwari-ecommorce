<?php
namespace ManufacturingManagementApi\Controllers;

use WP_REST_Response;

class BaseController {
    
    protected function success(string $message, $data = [], int $status = 200) {
        return new WP_REST_Response([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function error(string $message, array $errors = [], int $status = 400) {
        return new WP_REST_Response([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}
