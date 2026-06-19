<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\LeadRepository;
use WorkspaceErpApi\Repositories\OpportunityRepository;
use WorkspaceErpApi\Services\AuthService;
use WP_REST_Request;

class CrmController extends BaseController {
    private $leadRepo;
    private $oppRepo;

    public function __construct() {
        $this->leadRepo = new LeadRepository();
        $this->oppRepo = new OpportunityRepository();
    }

    public function indexLeads(WP_REST_Request $request) {
        $params = $request->get_params();
        $result = $this->leadRepo->findAll($params, ['id', 'lead_code', 'company_name', 'status'], ['lead_code', 'company_name', 'contact_person', 'email']);
        return $this->success('Leads fetched successfully', $result);
    }

    public function createLead(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['company_name']) || empty($params['contact_person'])) {
            return $this->error('Validation failed: company_name and contact_person are required.');
        }

        $code = 'LEAD-' . rand(1000, 9999);
        $data = [
            'lead_code' => $code,
            'company_name' => sanitize_text_field($params['company_name']),
            'contact_person' => sanitize_text_field($params['contact_person']),
            'email' => isset($params['email']) ? sanitize_email($params['email']) : '',
            'mobile' => isset($params['mobile']) ? sanitize_text_field($params['mobile']) : '',
            'source' => isset($params['source']) ? sanitize_text_field($params['source']) : 'Direct',
            'inquiry_type' => isset($params['inquiry_type']) ? sanitize_text_field($params['inquiry_type']) : 'Coworking',
            'seats_required' => isset($params['seats_required']) ? intval($params['seats_required']) : 1,
            'budget_range' => isset($params['budget_range']) ? sanitize_text_field($params['budget_range']) : '',
            'preferred_location' => isset($params['preferred_location']) ? sanitize_text_field($params['preferred_location']) : '',
            'status' => 'NEW',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->leadRepo->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s']);
        if (!$id) return $this->error('Failed to create lead.');

        AuthService::logActivity(get_current_user_id(), 'CREATE_LEAD', "Created lead $code (ID: $id)");
        return $this->success('Lead created successfully', array_merge(['id' => $id], $data), 201);
    }

    public function updateLead(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $lead = $this->leadRepo->findById($id);
        if (!$lead) return $this->error('Lead not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['company_name'])) { $update['company_name'] = sanitize_text_field($params['company_name']); $formats[] = '%s'; }
        if (isset($params['contact_person'])) { $update['contact_person'] = sanitize_text_field($params['contact_person']); $formats[] = '%s'; }
        if (isset($params['email'])) { $update['email'] = sanitize_email($params['email']); $formats[] = '%s'; }
        if (isset($params['mobile'])) { $update['mobile'] = sanitize_text_field($params['mobile']); $formats[] = '%s'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');

        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->leadRepo->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update lead.');

        AuthService::logActivity(get_current_user_id(), 'UPDATE_LEAD', "Updated lead ID: $id");
        return $this->success('Lead updated successfully', $this->leadRepo->findById($id));
    }

    public function deleteLead(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $lead = $this->leadRepo->findById($id);
        if (!$lead) return $this->error('Lead not found.', [], 404);

        $this->leadRepo->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DELETE_LEAD', "Soft deleted lead ID: $id");
        return $this->success('Lead deleted successfully');
    }

    public function indexOpportunities(WP_REST_Request $request) {
        $params = $request->get_params();
        $result = $this->oppRepo->findAll($params, ['id', 'opportunity_name', 'stage', 'probability'], ['opportunity_name']);
        return $this->success('Opportunities fetched successfully', $result);
    }
}
