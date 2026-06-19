<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\NotificationRepository;
use WP_REST_Request;

class NotificationController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new NotificationRepository();
    }

    public function index(WP_REST_Request $request) {
        return $this->success('Notifications list fetched', $this->repository->findAll($request->get_params(), ['id', 'recipient'], ['recipient', 'message']));
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['recipient']) || empty($params['message'])) return $this->error('recipient and message are required.');

        $data = [
            'type' => isset($params['type']) ? sanitize_text_field($params['type']) : 'SYSTEM',
            'recipient' => sanitize_text_field($params['recipient']),
            'subject' => isset($params['subject']) ? sanitize_text_field($params['subject']) : 'System Notification',
            'message' => sanitize_textarea_field($params['message']),
            'channel' => isset($params['channel']) ? sanitize_text_field($params['channel']) : 'EMAIL',
            'status' => 'SENT',
            'sent_at' => current_time('mysql'),
            'created_at' => current_time('mysql')
        ];
        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        return $this->success('Notification created successfully', array_merge(['id' => $id], $data), 201);
    }
}
