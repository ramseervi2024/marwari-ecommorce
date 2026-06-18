<?php
namespace CrmManagementApi\Controllers;

use CrmManagementApi\Repositories\LeadRepository;
use CrmManagementApi\Services\AuthService;
use WP_REST_Request;

if (!defined('ABSPATH')) {
    exit;
}

class CommunicationController extends BaseController {
    private $leadRepository;

    public function __construct() {
        $this->leadRepository = new LeadRepository();
    }

    // =========================================================================
    // CALL LOGS
    // =========================================================================

    /**
     * GET /call-logs
     */
    public function getCallLogs(WP_REST_Request $request) {
        global $wpdb;
        $params  = $request->get_params();
        $table   = $wpdb->prefix . 'crm_call_logs';
        $page    = max(1, intval($params['page']  ?? 1));
        $limit   = max(1, min(100, intval($params['limit'] ?? 10)));
        $offset  = ($page - 1) * $limit;
        $lead_id = isset($params['lead_id']) ? intval($params['lead_id']) : null;

        $where = $lead_id ? $wpdb->prepare('WHERE lead_id = %d', $lead_id) : '';

        $total = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table $where");
        $rows  = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table $where ORDER BY call_date DESC LIMIT %d OFFSET %d",
            $limit, $offset
        ), ARRAY_A);

        foreach ($rows as &$row) {
            $lead = $this->leadRepository->findById($row['lead_id']);
            $row['lead_name'] = $lead ? $lead['first_name'] . ' ' . $lead['last_name'] : '';
            $user = $row['caller_id'] ? get_userdata($row['caller_id']) : null;
            $row['caller_name'] = $user ? $user->display_name : 'Unknown';
        }

        return $this->success('Call logs retrieved.', [
            'total' => $total, 'page' => $page, 'limit' => $limit,
            'pages' => ceil($total / $limit), 'data'  => $rows ?: []
        ]);
    }

    /**
     * POST /call-logs
     */
    public function createCallLog(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_json_params();

        if (empty($params['lead_id'])) {
            return $this->error('lead_id is required.');
        }

        $lead = $this->leadRepository->findById(intval($params['lead_id']));
        if (!$lead) {
            return $this->error('Lead not found.', [], 404);
        }

        $table = $wpdb->prefix . 'crm_call_logs';
        $wpdb->insert($table, [
            'lead_id'        => intval($params['lead_id']),
            'caller_id'      => get_current_user_id(),
            'call_date'      => sanitize_text_field($params['call_date'] ?? current_time('mysql')),
            'duration'       => intval($params['duration'] ?? 0),
            'notes'          => sanitize_textarea_field($params['notes'] ?? ''),
            'recording_url'  => esc_url_raw($params['recording_url'] ?? ''),
        ], ['%d', '%d', '%s', '%d', '%s', '%s']);

        $id = $wpdb->insert_id;
        AuthService::logActivity(get_current_user_id(), 'CALLLOG_CREATE', "Logged call for lead ID: {$params['lead_id']}");

        return $this->success('Call log created.', $wpdb->get_row("SELECT * FROM $table WHERE id = $id", ARRAY_A), 201);
    }

    /**
     * PUT /call-logs/{id}
     */
    public function updateCallLog(WP_REST_Request $request) {
        global $wpdb;
        $id = intval($request->get_param('id'));
        $table = $wpdb->prefix . 'crm_call_logs';
        $log = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id), ARRAY_A);

        if (!$log) {
            return $this->error('Call log not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data   = [];
        $fmt    = [];

        if (isset($params['duration']))      { $data['duration']      = intval($params['duration']);                 $fmt[] = '%d'; }
        if (isset($params['notes']))         { $data['notes']         = sanitize_textarea_field($params['notes']);   $fmt[] = '%s'; }
        if (isset($params['recording_url'])) { $data['recording_url'] = esc_url_raw($params['recording_url']);       $fmt[] = '%s'; }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $wpdb->update($table, $data, ['id' => $id], $fmt, ['%d']);
        return $this->success('Call log updated.', $wpdb->get_row("SELECT * FROM $table WHERE id = $id", ARRAY_A));
    }

    /**
     * DELETE /call-logs/{id}
     */
    public function deleteCallLog(WP_REST_Request $request) {
        global $wpdb;
        $id = intval($request->get_param('id'));
        $table = $wpdb->prefix . 'crm_call_logs';

        if (!$wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE id = %d", $id))) {
            return $this->error('Call log not found.', [], 404);
        }

        $wpdb->delete($table, ['id' => $id], ['%d']);
        AuthService::logActivity(get_current_user_id(), 'CALLLOG_DELETE', "Deleted call log ID: $id");
        return $this->success('Call log deleted.');
    }

    // =========================================================================
    // MEETINGS
    // =========================================================================

    /**
     * GET /meetings
     */
    public function getMeetings(WP_REST_Request $request) {
        global $wpdb;
        $params  = $request->get_params();
        $table   = $wpdb->prefix . 'crm_meetings';
        $page    = max(1, intval($params['page']  ?? 1));
        $limit   = max(1, min(100, intval($params['limit'] ?? 10)));
        $offset  = ($page - 1) * $limit;
        $lead_id = isset($params['lead_id']) ? intval($params['lead_id']) : null;

        $where = $lead_id ? $wpdb->prepare('WHERE lead_id = %d', $lead_id) : '';

        $total = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table $where");
        $rows  = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table $where ORDER BY meeting_date DESC, meeting_time DESC LIMIT %d OFFSET %d",
            $limit, $offset
        ), ARRAY_A);

        foreach ($rows as &$row) {
            $lead = $this->leadRepository->findById($row['lead_id']);
            $row['lead_name'] = $lead ? $lead['first_name'] . ' ' . $lead['last_name'] : '';
            $host = $row['host_id'] ? get_userdata($row['host_id']) : null;
            $row['host_name'] = $host ? $host->display_name : 'Unknown';
        }

        return $this->success('Meetings retrieved.', [
            'total' => $total, 'page' => $page, 'limit' => $limit,
            'pages' => ceil($total / $limit), 'data'  => $rows ?: []
        ]);
    }

    /**
     * POST /meetings
     */
    public function createMeeting(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_json_params();

        if (empty($params['lead_id']) || empty($params['title']) || empty($params['meeting_date'])) {
            return $this->error('lead_id, title, and meeting_date are required.');
        }

        $lead = $this->leadRepository->findById(intval($params['lead_id']));
        if (!$lead) {
            return $this->error('Lead not found.', [], 404);
        }

        $table = $wpdb->prefix . 'crm_meetings';
        $wpdb->insert($table, [
            'lead_id'      => intval($params['lead_id']),
            'host_id'      => get_current_user_id(),
            'title'        => sanitize_text_field($params['title']),
            'meeting_date' => sanitize_text_field($params['meeting_date']),
            'meeting_time' => sanitize_text_field($params['meeting_time'] ?? ''),
            'notes'        => sanitize_textarea_field($params['notes'] ?? ''),
            'status'       => sanitize_text_field($params['status'] ?? 'Scheduled'),
        ], ['%d', '%d', '%s', '%s', '%s', '%s', '%s']);

        $id = $wpdb->insert_id;
        AuthService::logActivity(get_current_user_id(), 'MEETING_CREATE', "Scheduled meeting: {$params['title']}");
        return $this->success('Meeting scheduled.', $wpdb->get_row("SELECT * FROM $table WHERE id = $id", ARRAY_A), 201);
    }

    /**
     * PUT /meetings/{id}
     */
    public function updateMeeting(WP_REST_Request $request) {
        global $wpdb;
        $id = intval($request->get_param('id'));
        $table = $wpdb->prefix . 'crm_meetings';
        $meeting = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id), ARRAY_A);

        if (!$meeting) {
            return $this->error('Meeting not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data   = [];
        $fmt    = [];

        if (isset($params['title']))        { $data['title']        = sanitize_text_field($params['title']);        $fmt[] = '%s'; }
        if (isset($params['meeting_date'])) { $data['meeting_date'] = sanitize_text_field($params['meeting_date']); $fmt[] = '%s'; }
        if (isset($params['meeting_time'])) { $data['meeting_time'] = sanitize_text_field($params['meeting_time']); $fmt[] = '%s'; }
        if (isset($params['notes']))        { $data['notes']        = sanitize_textarea_field($params['notes']);    $fmt[] = '%s'; }
        if (isset($params['status']))       { $data['status']       = sanitize_text_field($params['status']);       $fmt[] = '%s'; }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $wpdb->update($table, $data, ['id' => $id], $fmt, ['%d']);
        AuthService::logActivity(get_current_user_id(), 'MEETING_UPDATE', "Updated meeting ID: $id");
        return $this->success('Meeting updated.', $wpdb->get_row("SELECT * FROM $table WHERE id = $id", ARRAY_A));
    }

    /**
     * DELETE /meetings/{id}
     */
    public function deleteMeeting(WP_REST_Request $request) {
        global $wpdb;
        $id = intval($request->get_param('id'));
        $table = $wpdb->prefix . 'crm_meetings';

        if (!$wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE id = %d", $id))) {
            return $this->error('Meeting not found.', [], 404);
        }

        $wpdb->delete($table, ['id' => $id], ['%d']);
        AuthService::logActivity(get_current_user_id(), 'MEETING_DELETE', "Deleted meeting ID: $id");
        return $this->success('Meeting deleted.');
    }

    // =========================================================================
    // WHATSAPP
    // =========================================================================

    /**
     * POST /whatsapp/send
     */
    public function sendWhatsApp(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_json_params();

        if (empty($params['recipient_number']) || empty($params['message'])) {
            return $this->error('recipient_number and message are required.');
        }

        $lead_id = isset($params['lead_id']) ? intval($params['lead_id']) : null;
        $number  = sanitize_text_field($params['recipient_number']);
        $msg     = sanitize_textarea_field($params['message']);

        $table_whatsapp = $wpdb->prefix . 'crm_whatsapp_logs';
        $inserted = $wpdb->insert($table_whatsapp, [
            'lead_id'          => $lead_id,
            'message'          => $msg,
            'recipient_number' => $number,
            'status'           => 'Sent',
            'sent_by'          => get_current_user_id()
        ], ['%d', '%s', '%s', '%s', '%d']);

        if (!$inserted) {
            return $this->error('Failed to log WhatsApp message.');
        }

        // Generate WhatsApp deep link for convenience
        $wa_link = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $number) . '?text=' . rawurlencode($msg);

        AuthService::logActivity(get_current_user_id(), 'WHATSAPP_SEND', "WhatsApp reminder sent to $number");

        return $this->success('WhatsApp message logged successfully.', [
            'id'               => $wpdb->insert_id,
            'recipient_number' => $number,
            'message'          => $msg,
            'status'           => 'Sent',
            'wa_link'          => $wa_link,
        ], 201);
    }

    /**
     * GET /whatsapp/history
     */
    public function getWhatsAppHistory(WP_REST_Request $request) {
        global $wpdb;
        $params  = $request->get_params();
        $table   = $wpdb->prefix . 'crm_whatsapp_logs';
        $lead_id = isset($params['lead_id']) ? intval($params['lead_id']) : null;

        $query = "SELECT * FROM $table";
        $args  = [];
        if ($lead_id) {
            $query .= " WHERE lead_id = %d";
            $args[] = $lead_id;
        }
        $query .= " ORDER BY created_at DESC LIMIT 50";

        $rows = $wpdb->get_results($args ? $wpdb->prepare($query, $args) : $query, ARRAY_A);

        foreach ($rows as &$row) {
            $user = $row['sent_by'] ? get_userdata($row['sent_by']) : null;
            $row['sender_name'] = $user ? $user->display_name : 'System';
        }

        return $this->success('WhatsApp logs retrieved.', $rows ?: []);
    }

    // =========================================================================
    // EMAIL
    // =========================================================================

    /**
     * POST /email/send
     */
    public function sendEmail(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_json_params();

        if (empty($params['recipient_email']) || empty($params['subject']) || empty($params['message'])) {
            return $this->error('recipient_email, subject, and message are required.');
        }

        $lead_id = isset($params['lead_id']) ? intval($params['lead_id']) : null;
        $email   = sanitize_email($params['recipient_email']);
        $subject = sanitize_text_field($params['subject']);
        $msg     = sanitize_textarea_field($params['message']);

        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        $smtp_from_email = get_option('crm_smtp_from_email');
        $smtp_from_name  = get_option('crm_smtp_from_name', 'CRM ERP');
        if (!empty($smtp_from_email)) {
            $headers[] = 'From: ' . $smtp_from_name . ' <' . $smtp_from_email . '>';
        }

        $sent   = wp_mail($email, $subject, $msg, $headers);
        $status = $sent ? 'Sent' : 'Failed';

        $table_email = $wpdb->prefix . 'crm_email_logs';
        $wpdb->insert($table_email, [
            'lead_id'         => $lead_id,
            'subject'         => $subject,
            'message'         => $msg,
            'recipient_email' => $email,
            'status'          => $status,
            'sent_by'         => get_current_user_id()
        ], ['%d', '%s', '%s', '%s', '%s', '%d']);

        if ($status === 'Failed') {
            return $this->error('Failed to send email. Check SMTP settings.');
        }

        AuthService::logActivity(get_current_user_id(), 'EMAIL_SEND', "Email sent to $email: $subject");

        return $this->success('Email sent successfully.', [
            'id'               => $wpdb->insert_id,
            'recipient_email'  => $email,
            'subject'          => $subject,
            'status'           => 'Sent'
        ], 201);
    }

    /**
     * GET /email/history
     */
    public function getEmailHistory(WP_REST_Request $request) {
        global $wpdb;
        $params  = $request->get_params();
        $table   = $wpdb->prefix . 'crm_email_logs';
        $lead_id = isset($params['lead_id']) ? intval($params['lead_id']) : null;

        $query = "SELECT * FROM $table";
        $args  = [];
        if ($lead_id) {
            $query .= " WHERE lead_id = %d";
            $args[] = $lead_id;
        }
        $query .= " ORDER BY created_at DESC LIMIT 50";

        $rows = $wpdb->get_results($args ? $wpdb->prepare($query, $args) : $query, ARRAY_A);

        foreach ($rows as &$row) {
            $user = $row['sent_by'] ? get_userdata($row['sent_by']) : null;
            $row['sender_name'] = $user ? $user->display_name : 'System';
        }

        return $this->success('Email logs retrieved.', $rows ?: []);
    }
}
