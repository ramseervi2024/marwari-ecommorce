<?php
namespace RealEstateManagementApi\Controllers;

use RealEstateManagementApi\Repositories\LeadRepository;
use RealEstateManagementApi\Services\AuthService;
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
        $allowed_sorts = ['id', 'lead_number', 'name', 'budget', 'lead_status', 'follow_up_date', 'created_at'];
        $search_fields = ['lead_number', 'name', 'mobile', 'email', 'city', 'property_interest'];
        
        $extra_filters = [];
        if (isset($params['lead_status'])) {
            $extra_filters['lead_status'] = sanitize_text_field($params['lead_status']);
        }
        if (isset($params['source'])) {
            $extra_filters['source'] = sanitize_text_field($params['source']);
        }
        if (isset($params['assigned_to'])) {
            $extra_filters['assigned_to'] = intval($params['assigned_to']);
        }

        $results = $this->leadRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Leads retrieved successfully.', $results);
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

        if (empty($params['name'])) {
            return $this->error('Validation failed: name is required.');
        }

        // Generate Lead Number
        $lead_number = 'LD-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        while ($this->leadRepository->existsLeadNumber($lead_number)) {
            $lead_number = 'LD-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'lead_number' => $lead_number,
            'name' => sanitize_text_field($params['name']),
            'mobile' => sanitize_text_field($params['mobile'] ?? ''),
            'email' => sanitize_email($params['email'] ?? ''),
            'source' => sanitize_text_field($params['source'] ?? 'Website'),
            'budget' => isset($params['budget']) ? floatval($params['budget']) : 0.00,
            'property_interest' => sanitize_text_field($params['property_interest'] ?? ''),
            'city' => sanitize_text_field($params['city'] ?? ''),
            'assigned_to' => !empty($params['assigned_to']) ? intval($params['assigned_to']) : null,
            'lead_status' => sanitize_text_field($params['lead_status'] ?? 'New'),
            'follow_up_date' => !empty($params['follow_up_date']) ? sanitize_text_field($params['follow_up_date']) : null,
            'remarks' => sanitize_textarea_field($params['remarks'] ?? '')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%d', '%s', '%s', '%s'];
        $inserted_id = $this->leadRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create lead.');
        }

        // Insert into pipeline table if required
        global $wpdb;
        $table_pipeline = $wpdb->prefix . 'realestate_pipeline';
        $wpdb->insert($table_pipeline, [
            'lead_id' => $inserted_id,
            'stage' => 'Lead',
            'deal_value' => $data['budget'],
            'expected_closure_date' => $data['follow_up_date'],
            'status' => 'Active'
        ]);

        AuthService::logActivity(get_current_user_id(), 'LEAD_CREATE', "Created lead code $lead_number ($inserted_id)");

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
        
        $fields = ['name', 'mobile', 'email', 'source', 'budget', 'property_interest', 'city', 'assigned_to', 'lead_status', 'follow_up_date', 'remarks'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'budget') {
                    $data[$field] = floatval($params[$field]);
                    $formats[] = '%f';
                } elseif ($field === 'assigned_to') {
                    $data[$field] = !empty($params[$field]) ? intval($params[$field]) : null;
                    $formats[] = '%d';
                } elseif ($field === 'email') {
                    $data[$field] = sanitize_email($params[$field]);
                    $formats[] = '%s';
                } elseif ($field === 'remarks') {
                    $data[$field] = sanitize_textarea_field($params[$field]);
                    $formats[] = '%s';
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                    $formats[] = '%s';
                }
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->leadRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update lead.');
        }

        // Keep sales pipeline in sync with lead status
        if (isset($data['lead_status'])) {
            global $wpdb;
            $table_pipeline = $wpdb->prefix . 'realestate_pipeline';
            $stage = $data['lead_status'];
            // Map lead_status to pipeline stage
            if ($stage === 'Site Visit Scheduled') $stage = 'Site Visit';
            if ($stage === 'Booked') $stage = 'Booking';
            
            $wpdb->update($table_pipeline, [
                'stage' => $stage,
                'deal_value' => isset($data['budget']) ? $data['budget'] : $lead['budget'],
                'expected_closure_date' => isset($data['follow_up_date']) ? $data['follow_up_date'] : $lead['follow_up_date']
            ], ['lead_id' => $id]);
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

        global $wpdb;
        $table_pipeline = $wpdb->prefix . 'realestate_pipeline';
        $wpdb->update($table_pipeline, ['deleted_at' => current_time('mysql')], ['lead_id' => $id]);

        AuthService::logActivity(get_current_user_id(), 'LEAD_DELETE', "Soft deleted lead ID: $id ($lead[lead_number])");

        return $this->success('Lead deleted successfully.');
    }
}
