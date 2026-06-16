<?php
namespace RestaurantManagementApi\Controllers;

use WP_REST_Request;

class NotificationController extends BaseController {
    
    public function sendEmail(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['to']) || empty($params['subject']) || empty($params['body'])) {
            return $this->error('Validation failed: to, subject, and body are required.');
        }

        $result = wp_mail(
            sanitize_email($params['to']),
            sanitize_text_field($params['subject']),
            wp_kses_post($params['body'])
        );

        if (!$result) {
            return $this->error('WordPress mail function failed to send.');
        }

        return $this->success('Email notification processed successfully.');
    }

    public function sendSms(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['mobile']) || empty($params['message'])) {
            return $this->error('Validation failed: mobile and message are required.');
        }

        // Simulating SMS Gateway integration
        return $this->success('SMS notification sent successfully (Simulated Gateway).', [
            'recipient' => sanitize_text_field($params['mobile']),
            'gateway_status' => 'DELIVERED',
            'sms_id' => 'SMS-' . strtoupper(bin2hex(random_bytes(4)))
        ]);
    }

    public function sendWhatsapp(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['mobile']) || empty($params['message'])) {
            return $this->error('Validation failed: mobile and message are required.');
        }

        // Simulating WhatsApp Business API integration
        return $this->success('WhatsApp message sent successfully (Simulated Business API).', [
            'recipient' => sanitize_text_field($params['mobile']),
            'api_status' => 'SENT',
            'message_id' => 'WA-' . strtoupper(bin2hex(random_bytes(4)))
        ]);
    }

    public function sendPush(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['title']) || empty($params['message'])) {
            return $this->error('Validation failed: title and message are required.');
        }

        // Simulating FCM/OneSignal Push Notification integration
        return $this->success('Push notification broadcasted successfully (Simulated FCM Push).', [
            'topic' => sanitize_text_field($params['topic'] ?? 'all_devices'),
            'broadcast_status' => 'BROADCASTED',
            'push_id' => 'PUSH-' . strtoupper(bin2hex(random_bytes(4)))
        ]);
    }
}
