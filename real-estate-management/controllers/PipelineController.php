<?php
namespace RealEstateManagementApi\Controllers;

use RealEstateManagementApi\Repositories\PipelineRepository;
use RealEstateManagementApi\Repositories\LeadRepository;
use RealEstateManagementApi\Services\AuthService;
use WP_REST_Request;

class PipelineRepositoryController extends BaseController {
    // Wait, let's keep name as PipelineController for the class!
}

class PipelineController extends BaseController {
    private $pipelineRepository;
    private $leadRepository;

    public function __construct() {
        $this->pipelineRepository = new PipelineRepository();
        $this->leadRepository = new LeadRepository();
    }

    /**
     * GET /pipeline
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'lead_id', 'stage', 'deal_value', 'expected_closure_date', 'status', 'created_at'];
        $search_fields = ['stage', 'status'];
        
        $extra_filters = [];
        if (isset($params['lead_id'])) {
            $extra_filters['lead_id'] = intval($params['lead_id']);
        }
        if (isset($params['stage'])) {
            $extra_filters['stage'] = sanitize_text_field($params['stage']);
        }

        $results = $this->pipelineRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        // Enrich data with lead details
        if (!empty($results['data'])) {
            foreach ($results['data'] as &$row) {
                $lead = $this->leadRepository->findById(intval($row['lead_id']));
                $row['lead_name'] = $lead ? $lead['name'] : '';
                $row['lead_status'] = $lead ? $lead['lead_status'] : '';
            }
        }

        return $this->success('Sales pipeline retrieved successfully.', $results);
    }

    /**
     * POST /pipeline
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['lead_id']) || empty($params['stage'])) {
            return $this->error('Validation failed: lead_id and stage are required.');
        }

        $data = [
            'lead_id' => intval($params['lead_id']),
            'stage' => sanitize_text_field($params['stage']),
            'deal_value' => isset($params['deal_value']) ? floatval($params['deal_value']) : 0.00,
            'expected_closure_date' => !empty($params['expected_closure_date']) ? sanitize_text_field($params['expected_closure_date']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'Active')
        ];

        $formats = ['%d', '%s', '%f', '%s', '%s'];
        $inserted_id = $this->pipelineRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create sales pipeline record.');
        }

        AuthService::logActivity(get_current_user_id(), 'PIPELINE_CREATE', "Created sales pipeline record $inserted_id for lead $params[lead_id]");

        return $this->success('Sales pipeline record created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /pipeline/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $pipe = $this->pipelineRepository->findById($id);

        if (!$pipe) {
            return $this->error('Sales pipeline record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['stage', 'deal_value', 'expected_closure_date', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'deal_value') {
                    $data[$field] = floatval($params[$field]);
                    $formats[] = '%f';
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                    $formats[] = '%s';
                }
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->pipelineRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update sales pipeline record.');
        }

        // Keep lead status in sync if stage changed
        if (isset($data['stage'])) {
            $lead_status = $data['stage'];
            if ($lead_status === 'Site Visit') $lead_status = 'Site Visit Scheduled';
            if ($lead_status === 'Booking') $lead_status = 'Booked';

            $this->leadRepository->update(intval($pipe['lead_id']), [
                'lead_status' => $lead_status
            ], ['%s']);
        }

        AuthService::logActivity(get_current_user_id(), 'PIPELINE_UPDATE', "Updated sales pipeline ID: $id");

        return $this->success('Sales pipeline record updated successfully.', $this->pipelineRepository->findById($id));
    }
}
