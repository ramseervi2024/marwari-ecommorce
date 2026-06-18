<?php
namespace GymErpApi\Controllers;
if (!defined('ABSPATH')) exit;
class BaseController {
    protected function success(string $message, $data = null, int $status = 200): \WP_REST_Response {
        $resp = ['success' => true, 'message' => $message];
        if ($data !== null) $resp['data'] = $data;
        return new \WP_REST_Response($resp, $status);
    }
    protected function error(string $message, $data = [], int $status = 400): \WP_REST_Response {
        return new \WP_REST_Response(['success' => false, 'message' => $message, 'data' => $data], $status);
    }
}
