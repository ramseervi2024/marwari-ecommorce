<?php
namespace ServiceManagementApi\Controllers;

use ServiceManagementApi\Repositories\LeadRepository;
use ServiceManagementApi\Services\AuthService;
use WP_REST_Request;

class LeadController extends BaseController {
    private $leadRepository;

    public function __construct() {
        $this->leadRepository = new LeadRepository();
    }

    /**
     * GET /leads
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'lead_name', 'customer_name', 'status', 'created_at'];
        $search_fields = ['lead_name', 'customer_name', 'email', 'phone', 'status', 'source', 'requirements'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->leadRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Leads list retrieved successfully.', $results);
    }

    /**
     * GET /leads/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $lead = $this->leadRepository->findById($id);

        if (!$lead) {
            return $this->error('Lead not found.', [], 404);
        }

        return $this->success('Lead retrieved successfully.', $lead);
    }

    /**
     * POST /leads
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['lead_name']) || empty($params['customer_name'])) {
            return $this->error('Validation failed: lead_name and customer_name are required.');
        }

        $data = [
            'lead_name' => sanitize_text_field($params['lead_name']),
            'customer_name' => sanitize_text_field($params['customer_name']),
            'email' => sanitize_email($params['email'] ?? ''),
            'phone' => sanitize_text_field($params['phone'] ?? ''),
            'status' => sanitize_text_field($params['status'] ?? 'Pending'),
            'source' => sanitize_text_field($params['source'] ?? 'Direct'),
            'requirements' => sanitize_textarea_field($params['requirements'] ?? '')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        $inserted_id = $this->leadRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create lead.');
        }

        AuthService::logActivity(get_current_user_id(), 'LEAD_CREATE', "Created lead: {$data['lead_name']} for {$data['customer_name']}");

        return $this->success('Lead created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /leads/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $lead = $this->leadRepository->findById($id);

        if (!$lead) {
            return $this->error('Lead not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'lead_name' => '%s',
            'customer_name' => '%s',
            'email' => '%s',
            'phone' => '%s',
            'status' => '%s',
            'source' => '%s',
            'requirements' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($field === 'email') {
                    $data[$field] = sanitize_email($params[$field]);
                } elseif ($field === 'requirements') {
                    $data[$field] = sanitize_textarea_field($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->leadRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update lead details.');
        }

        AuthService::logActivity(get_current_user_id(), 'LEAD_UPDATE', "Updated lead ID: $id");

        return $this->success('Lead updated successfully.', $this->leadRepository->findById($id));
    }

    /**
     * DELETE /leads/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $lead = $this->leadRepository->findById($id);

        if (!$lead) {
            return $this->error('Lead not found.', [], 404);
        }

        $deleted = $this->leadRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete lead.');
        }

        AuthService::logActivity(get_current_user_id(), 'LEAD_DELETE', "Soft deleted lead ID: $id");

        return $this->success('Lead deleted successfully.');
    }
}
