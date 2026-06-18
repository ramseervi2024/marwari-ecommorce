<?php
namespace CrmManagementApi\Controllers;

use CrmManagementApi\Repositories\LeadRepository;
use CrmManagementApi\Services\AuthService;
use WP_REST_Request;

class LeadController extends BaseController {
    private $leadRepository;

    public function __construct() {
        $this->leadRepository = new LeadRepository();
    }

    /**
     * GET /leads
     */
    public function getLeads(WP_REST_Request $request) {
        $params = $request->get_params();
        $current_user = wp_get_current_user();

        $allowed_sorts = ['id', 'lead_number', 'first_name', 'last_name', 'company_name', 'email', 'lead_status', 'created_at'];
        $search_fields = ['lead_number', 'first_name', 'last_name', 'company_name', 'email', 'mobile'];
        $extra_filters = [];

        if (isset($params['lead_status'])) {
            $extra_filters['lead_status'] = sanitize_text_field($params['lead_status']);
        }
        if (isset($params['lead_source'])) {
            $extra_filters['lead_source'] = sanitize_text_field($params['lead_source']);
        }

        // Executive / Telecaller restriction: see only assigned leads
        if (!current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            $extra_filters['assigned_to'] = $current_user->ID;
        } elseif (isset($params['assigned_to'])) {
            $extra_filters['assigned_to'] = intval($params['assigned_to']);
        }

        $results = $this->leadRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);

        // Map assigned user details
        foreach ($results['data'] as &$row) {
            if ($row['assigned_to']) {
                $assigned = get_userdata($row['assigned_to']);
                $row['assigned_name'] = $assigned ? $assigned->display_name : 'Unknown';
            } else {
                $row['assigned_name'] = 'Unassigned';
            }
        }

        return $this->success('Leads list retrieved.', $results);
    }

    /**
     * GET /leads/{id}
     */
    public function getLead(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $lead = $this->leadRepository->findById($id);

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

        if ($lead['assigned_to']) {
            $assigned = get_userdata($lead['assigned_to']);
            $lead['assigned_name'] = $assigned ? $assigned->display_name : 'Unknown';
        } else {
            $lead['assigned_name'] = 'Unassigned';
        }

        return $this->success('Lead details retrieved.', $lead);
    }

    /**
     * POST /leads
     */
    public function createLead(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['first_name']) || empty($params['email']) || empty($params['mobile'])) {
            return $this->error('first_name, email, and mobile are required.');
        }

        // Auto generate lead number
        global $wpdb;
        $table_name = $wpdb->prefix . 'crm_leads';
        $max_id = (int)$wpdb->get_var("SELECT MAX(id) FROM $table_name") + 1;
        $lead_number = 'LD-' . date('Y') . '-' . sprintf('%04d', $max_id);

        $assigned_to = isset($params['assigned_to']) ? intval($params['assigned_to']) : get_current_user_id();

        $data = [
            'lead_number'  => $lead_number,
            'first_name'   => sanitize_text_field($params['first_name']),
            'last_name'    => sanitize_text_field($params['last_name'] ?? ''),
            'company_name' => sanitize_text_field($params['company_name'] ?? ''),
            'mobile'       => sanitize_text_field($params['mobile']),
            'email'        => sanitize_email($params['email']),
            'website'      => esc_url_raw($params['website'] ?? ''),
            'lead_source'  => sanitize_text_field($params['lead_source'] ?? 'Website'),
            'industry'     => sanitize_text_field($params['industry'] ?? ''),
            'city'         => sanitize_text_field($params['city'] ?? ''),
            'state'        => sanitize_text_field($params['state'] ?? ''),
            'assigned_to'  => $assigned_to,
            'lead_status'  => sanitize_text_field($params['lead_status'] ?? 'New'),
            'remarks'      => sanitize_textarea_field($params['remarks'] ?? ''),
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s'];

        $id = $this->leadRepository->create($data, $formats);
        if (!$id) {
            return $this->error('Failed to create lead.');
        }

        AuthService::logActivity(get_current_user_id(), 'LEAD_CREATE', "Created lead: $lead_number ($data[first_name])");

        return $this->success('Lead created successfully.', $this->leadRepository->findById($id), 201);
    }

    /**
     * PUT /leads/{id}
     */
    public function updateLead(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $lead = $this->leadRepository->findById($id);

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

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'first_name'   => '%s',
            'last_name'    => '%s',
            'company_name' => '%s',
            'mobile'       => '%s',
            'email'        => '%s',
            'website'      => '%s',
            'lead_source'  => '%s',
            'industry'     => '%s',
            'city'         => '%s',
            'state'        => '%s',
            'assigned_to'  => '%d',
            'lead_status'  => '%s',
            'remarks'      => '%s'
        ];

        foreach ($fields as $key => $fmt) {
            if (isset($params[$key])) {
                if ($key === 'email') {
                    $data[$key] = sanitize_email($params[$key]);
                } elseif ($key === 'website') {
                    $data[$key] = esc_url_raw($params[$key]);
                } elseif ($key === 'assigned_to') {
                    $data[$key] = intval($params[$key]);
                } elseif ($key === 'remarks') {
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

        $updated = $this->leadRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update lead.');
        }

        AuthService::logActivity(get_current_user_id(), 'LEAD_UPDATE', "Updated lead ID: $id ($lead[lead_number])");

        return $this->success('Lead updated successfully.', $this->leadRepository->findById($id));
    }

    /**
     * DELETE /leads/{id}
     */
    public function deleteLead(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $lead = $this->leadRepository->findById($id);

        if (!$lead) {
            return $this->error('Lead not found.', [], 404);
        }

        // Only manager and admin can delete leads
        if (!current_user_can('manage_crm_settings') && !current_user_can('view_crm_reports')) {
            return $this->error('Access Denied.', [], 403);
        }

        $deleted = $this->leadRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete lead.');
        }

        AuthService::logActivity(get_current_user_id(), 'LEAD_DELETE', "Soft deleted lead ID: $id ($lead[lead_number])");

        return $this->success('Lead deleted successfully.');
    }
}
