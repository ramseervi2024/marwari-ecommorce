<?php
namespace RealEstateManagementApi\Controllers;

use WP_REST_Response;

class BaseController {
    
    /**
     * Send standard success REST response
     */
    protected function success(string $message, $data = [], int $status = 200) {
        return new WP_REST_Response([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Send standard error REST response
     */
    protected function error(string $message, array $errors = [], int $status = 400) {
        return new WP_REST_Response([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}
