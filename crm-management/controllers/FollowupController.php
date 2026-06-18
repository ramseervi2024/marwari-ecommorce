<?php
namespace CrmManagementApi\Controllers;

use CrmManagementApi\Repositories\FollowupRepository;
use CrmManagementApi\Repositories\LeadRepository;
use CrmManagementApi\Services\AuthService;
use WP_REST_Request;

class FollowupController extends BaseController {
    private $followupRepository;
    private $leadRepository;

    public function __construct() {
        $this->followupRepository = new FollowupRepository();
        $this->leadRepository     = new LeadRepository();
    }

    /**
     * GET /followups
     */
    public function getFollowups(WP_REST_Request $request) {
        $params = $request->get_params();
        $current_user = wp_get_current_user();

        $allowed_sorts = ['id', 'followup_date', 'followup_time', 'communication_type', 'status'];
        $extra_filters = [];

        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['communication_type'])) {
            $extra_filters['communication_type'] = sanitize_text_field($params['communication_type']);
        }
        if (isset($params['lead_id'])) {
            $extra_filters['lead_id'] = intval($params['lead_id']);
        }

        // Executive / Telecaller restriction: see only followups of their assigned leads
        if (!current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            global $wpdb;
            $table_leads = $wpdb->prefix . 'crm_leads';
            $assigned_lead_ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM $table_leads WHERE assigned_to = %d AND deleted_at IS NULL", $current_user->ID));
            
            if (empty($assigned_lead_ids)) {
                return $this->success('Follow-ups list (empty).', [
                    'total' => 0,
                    'page' => 1,
                    'limit' => 10,
                    'pages' => 0,
                    'data' => []
                ]);
            }
            
            // Build custom query in controller since BaseRepository doesn't support IN arrays directly
            $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
            $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
            $offset = ($page - 1) * $limit;
            $sort = isset($params['sort']) && in_array($params['sort'], $allowed_sorts) ? $params['sort'] : 'followup_date';
            $order = isset($params['order']) && strtoupper($params['order']) === 'DESC' ? 'DESC' : 'ASC';

            $in_clause = implode(',', array_map('intval', $assigned_lead_ids));
            $table_name = $wpdb->prefix . 'crm_followups';
            
            $where = "lead_id IN ($in_clause)";
            if (isset($extra_filters['status'])) {
                $where .= $wpdb->prepare(" AND status = %s", $extra_filters['status']);
            }
            if (isset($extra_filters['communication_type'])) {
                $where .= $wpdb->prepare(" AND communication_type = %s", $extra_filters['communication_type']);
            }
            if (isset($extra_filters['lead_id'])) {
                $where .= $wpdb->prepare(" AND lead_id = %d", $extra_filters['lead_id']);
            }

            $total = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE $where");
            $rows = $wpdb->get_results("SELECT * FROM $table_name WHERE $where ORDER BY $sort $order LIMIT $limit OFFSET $offset", ARRAY_A);

            $results = [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit),
                'data' => $rows ?: []
            ];
        } else {
            $results = $this->followupRepository->findAll($params, $allowed_sorts, [], $extra_filters);
        }

        // Map lead names
        foreach ($results['data'] as &$row) {
            $lead = $this->leadRepository->findById($row['lead_id']);
            if ($lead) {
                $row['lead_name']    = $lead['first_name'] . ' ' . $lead['last_name'];
                $row['company_name'] = $lead['company_name'];
            } else {
                $row['lead_name']    = 'Unknown Lead';
                $row['company_name'] = '';
            }
        }

        return $this->success('Follow-ups list retrieved.', $results);
    }

    /**
     * GET /followups/{id}
     */
    public function getFollowup(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $followup = $this->followupRepository->findById($id);
        if (!$followup) {
            return $this->error('Follow-up not found.', [], 404);
        }
        $lead = $this->leadRepository->findById($followup['lead_id']);
        $followup['lead_name']    = $lead ? $lead['first_name'] . ' ' . $lead['last_name'] : '';
        $followup['company_name'] = $lead ? $lead['company_name'] : '';
        return $this->success('Follow-up retrieved.', $followup);
    }

    /**
     * POST /followups
     */
    public function createFollowup(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['lead_id']) || empty($params['followup_date'])) {
            return $this->error('lead_id and followup_date are required.');
        }

        $lead_id = intval($params['lead_id']);
        $lead = $this->leadRepository->findById($lead_id);
        if (!$lead) {
            return $this->error('Lead not found.', [], 404);
        }

        // Privilege check
        $current_user = wp_get_current_user();
        if (!current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            if (intval($lead['assigned_to']) !== $current_user->ID) {
                return $this->error('Access Denied.', [], 403);
            }
        }

        $data = [
            'lead_id'            => $lead_id,
            'followup_date'      => sanitize_text_field($params['followup_date']),
            'followup_time'      => sanitize_text_field($params['followup_time'] ?? '12:00:00'),
            'communication_type' => sanitize_text_field($params['communication_type'] ?? 'Call'),
            'remarks'            => sanitize_textarea_field($params['remarks'] ?? ''),
            'next_followup_date' => !empty($params['next_followup_date']) ? sanitize_text_field($params['next_followup_date']) : null,
            'status'             => sanitize_text_field($params['status'] ?? 'Pending')
        ];

        $formats = ['%d', '%s', '%s', '%s', '%s', '%s', '%s'];

        $id = $this->followupRepository->create($data, $formats);
        if (!$id) {
            return $this->error('Failed to record follow-up.');
        }

        // Automatically update the lead status to "Follow-Up" if lead status was "New" or "Contacted"
        if (in_array($lead['lead_status'], ['New', 'Contacted'])) {
            $this->leadRepository->update($lead_id, ['lead_status' => 'Follow-Up'], ['%s']);
        }

        AuthService::logActivity(get_current_user_id(), 'FOLLOWUP_CREATE', "Logged follow-up for lead ID: $lead_id");

        return $this->success('Follow-up logged successfully.', $this->followupRepository->findById($id), 201);
    }

    /**
     * PUT /followups/{id}
     */
    public function updateFollowup(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $followup = $this->followupRepository->findById($id);

        if (!$followup) {
            return $this->error('Follow-up not found.', [], 404);
        }

        // Privilege check
        $lead = $this->leadRepository->findById($followup['lead_id']);
        $current_user = wp_get_current_user();
        if ($lead && !current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            if (intval($lead['assigned_to']) !== $current_user->ID) {
                return $this->error('Access Denied.', [], 403);
            }
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'followup_date'      => '%s',
            'followup_time'      => '%s',
            'communication_type' => '%s',
            'remarks'            => '%s',
            'next_followup_date' => '%s',
            'status'             => '%s'
        ];

        foreach ($fields as $key => $fmt) {
            if (isset($params[$key])) {
                if ($key === 'remarks') {
                    $data[$key] = sanitize_textarea_field($params[$key]);
                } else {
                    $data[$key] = sanitize_text_field($params[$key]);
                }
                $formats[] = $fmt;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->followupRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update follow-up.');
        }

        AuthService::logActivity(get_current_user_id(), 'FOLLOWUP_UPDATE', "Updated follow-up ID: $id");

        return $this->success('Follow-up updated successfully.', $this->followupRepository->findById($id));
    }

    /**
     * DELETE /followups/{id}
     */
    public function deleteFollowup(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $followup = $this->followupRepository->findById($id);

        if (!$followup) {
            return $this->error('Follow-up not found.', [], 404);
        }

        // Privilege check
        $lead = $this->leadRepository->findById($followup['lead_id']);
        $current_user = wp_get_current_user();
        if ($lead && !current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            if (intval($lead['assigned_to']) !== $current_user->ID) {
                return $this->error('Access Denied.', [], 403);
            }
        }

        $deleted = $this->followupRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete follow-up.');
        }

        AuthService::logActivity(get_current_user_id(), 'FOLLOWUP_DELETE', "Deleted follow-up ID: $id");

        return $this->success('Follow-up deleted successfully.');
    }
}
