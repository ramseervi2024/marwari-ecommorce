<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Services\AuthService;
use WP_REST_Request;

class NotificationController extends BaseController {
    
    public function sendEmail(WP_REST_Request $request) {
        $params = $request->get_json_params();
        $to = sanitize_email($params['to'] ?? '');
        $subject = sanitize_text_field($params['subject'] ?? 'Alert');
        $message = sanitize_textarea_field($params['message'] ?? '');

        if (empty($to) || empty($message)) {
            return $this->error('Validation failed: to and message are required.');
        }

        AuthService::logActivity(get_current_user_id(), 'ALERT_EMAIL', "Email simulation sent to $to: $subject");
        return $this->success("Email alert sent successfully (Simulation).");
    }

    public function sendSms(WP_REST_Request $request) {
        $params = $request->get_json_params();
        $mobile = sanitize_text_field($params['mobile'] ?? '');
        $message = sanitize_textarea_field($params['message'] ?? '');

        if (empty($mobile) || empty($message)) {
            return $this->error('Validation failed: mobile and message are required.');
        }

        AuthService::logActivity(get_current_user_id(), 'ALERT_SMS', "SMS simulation sent to $mobile");
        return $this->success("SMS alert sent successfully (Simulation).");
    }

    public function sendWhatsapp(WP_REST_Request $request) {
        $params = $request->get_json_params();
        $mobile = sanitize_text_field($params['mobile'] ?? '');
        $message = sanitize_textarea_field($params['message'] ?? '');

        if (empty($mobile) || empty($message)) {
            return $this->error('Validation failed: mobile and message are required.');
        }

        AuthService::logActivity(get_current_user_id(), 'ALERT_WHATSAPP', "WhatsApp simulation sent to $mobile");
        return $this->success("WhatsApp alert sent successfully (Simulation).");
    }
}
