<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\NotificationRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class NotificationController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new NotificationRepository();
    }

    /**
     * POST /notifications/email
     */
    public function sendEmail(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['recipient']) || empty($params['subject']) || empty($params['message'])) {
            return $this->error('Validation failed: recipient, subject, and message are required.');
        }

        $data = [
            'type' => 'EMAIL',
            'recipient' => sanitize_email($params['recipient']),
            'subject' => sanitize_text_field($params['subject']),
            'message' => wp_kses_post($params['message']),
            'status' => 'SENT',
            'sent_at' => current_time('mysql'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        // Trigger mock wp_mail call
        wp_mail($data['recipient'], $data['subject'], $data['message']);

        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        AuthService::logActivity(get_current_user_id(), 'SEND_NOTIFICATION_EMAIL', "Email sent to {$data['recipient']}");

        return $this->success('Email notification sent successfully', array_merge(['id' => $id], $data), 201);
    }

    /**
     * POST /notifications/sms
     */
    public function sendSMS(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['recipient']) || empty($params['message'])) {
            return $this->error('Validation failed: recipient (mobile) and message are required.');
        }

        $data = [
            'type' => 'SMS',
            'recipient' => sanitize_text_field($params['recipient']),
            'subject' => null,
            'message' => sanitize_text_field($params['message']),
            'status' => 'SENT',
            'sent_at' => current_time('mysql'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        AuthService::logActivity(get_current_user_id(), 'SEND_NOTIFICATION_SMS', "SMS sent to {$data['recipient']}");

        return $this->success('SMS notification sent successfully (Mock Gateway)', array_merge(['id' => $id], $data), 201);
    }

    /**
     * POST /notifications/push
     */
    public function sendPushNotification(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['recipient']) || empty($params['message'])) {
            return $this->error('Validation failed: recipient (device_token) and message are required.');
        }

        $data = [
            'type' => 'PUSH',
            'recipient' => sanitize_text_field($params['recipient']),
            'subject' => isset($params['title']) ? sanitize_text_field($params['title']) : 'School Update',
            'message' => sanitize_text_field($params['message']),
            'status' => 'SENT',
            'sent_at' => current_time('mysql'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        AuthService::logActivity(get_current_user_id(), 'SEND_NOTIFICATION_PUSH', "Push notification sent to token {$data['recipient']}");

        return $this->success('Push notification sent successfully (Mock FCM/APNS)', array_merge(['id' => $id], $data), 201);
    }

    /**
     * POST /notifications/whatsapp
     */
    public function sendWhatsApp(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['recipient']) || empty($params['message'])) {
            return $this->error('Validation failed: recipient (mobile) and message are required.');
        }

        $data = [
            'type' => 'WHATSAPP',
            'recipient' => sanitize_text_field($params['recipient']),
            'subject' => null,
            'message' => sanitize_text_field($params['message']),
            'status' => 'SENT',
            'sent_at' => current_time('mysql'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        AuthService::logActivity(get_current_user_id(), 'SEND_NOTIFICATION_WHATSAPP', "WhatsApp message sent to {$data['recipient']}");

        return $this->success('WhatsApp notification sent successfully (Mock Meta Business)', array_merge(['id' => $id], $data), 201);
    }
}
