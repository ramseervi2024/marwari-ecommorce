<?php
namespace CrmManagementApi\Controllers;

use WP_REST_Request;

if (!defined('ABSPATH')) {
    exit;
}

class BaseController {
    /**
     * Return a standard success response.
     */
    protected function success(string $message, $data = [], int $code = 200) {
        return new \WP_REST_Response([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Return a standard error response.
     */
    protected function error(string $message, $data = [], int $code = 400) {
        return new \WP_REST_Response([
            'success' => false,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
}
