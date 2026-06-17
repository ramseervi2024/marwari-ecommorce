<?php
namespace ConstructionManagementApi\Controllers;

use ConstructionManagementApi\Repositories\ContractorRepository;
use ConstructionManagementApi\Services\AuthService;
use WP_REST_Request;

class ContractorController extends BaseController {
    private $contractorRepository;

    public function __construct() {
        $this->contractorRepository = new ContractorRepository();
    }

    /**
     * GET /contractors
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'contractor_code', 'contractor_name', 'contract_value', 'status'];
        $search_fields = ['contractor_code', 'contractor_name', 'mobile', 'email', 'address', 'specialization'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['specialization'])) {
            $extra_filters['specialization'] = sanitize_text_field($params['specialization']);
        }

        $results = $this->contractorRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Contractors retrieved successfully.', $results);
    }

    /**
     * GET /contractors/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $contractor = $this->contractorRepository->findById($id);

        if (!$contractor) {
            return $this->error('Contractor not found.', [], 404);
        }

        return $this->success('Contractor retrieved successfully.', $contractor);
    }

    /**
     * POST /contractors
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['contractor_name'])) {
            return $this->error('Validation failed: contractor_name is required.');
        }

        $contractor_code = 'CON-' . strtoupper(substr(sanitize_key($params['contractor_name']), 0, 3)) . '-' . sprintf('%03d', rand(100, 999));
        while ($this->contractorRepository->existsContractorCode($contractor_code)) {
            $contractor_code = 'CON-' . strtoupper(substr(sanitize_key($params['contractor_name']), 0, 3)) . '-' . sprintf('%03d', rand(100, 999));
        }

        $data = [
            'contractor_code' => $contractor_code,
            'contractor_name' => sanitize_text_field($params['contractor_name']),
            'mobile' => sanitize_text_field($params['mobile'] ?? ''),
            'email' => sanitize_email($params['email'] ?? ''),
            'address' => sanitize_textarea_field($params['address'] ?? ''),
            'specialization' => sanitize_text_field($params['specialization'] ?? ''),
            'contract_value' => isset($params['contract_value']) ? floatval($params['contract_value']) : 0.00,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s'];
        $inserted_id = $this->contractorRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create contractor.');
        }

        AuthService::logActivity(get_current_user_id(), 'CONTRACTOR_CREATE', "Created contractor code $contractor_code ($inserted_id)");

        return $this->success('Contractor created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /contractors/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $contractor = $this->contractorRepository->findById($id);

        if (!$contractor) {
            return $this->error('Contractor not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['contractor_name', 'mobile', 'email', 'address', 'specialization', 'contract_value', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'contract_value') {
                    $data[$field] = floatval($params[$field]);
                    $formats[] = '%f';
                } elseif ($field === 'email') {
                    $data[$field] = sanitize_email($params[$field]);
                    $formats[] = '%s';
                } elseif ($field === 'address') {
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

        $updated = $this->contractorRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update contractor.');
        }

        AuthService::logActivity(get_current_user_id(), 'CONTRACTOR_UPDATE', "Updated contractor record ID: $id");

        return $this->success('Contractor updated successfully.', $this->contractorRepository->findById($id));
    }

    /**
     * DELETE /contractors/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $contractor = $this->contractorRepository->findById($id);

        if (!$contractor) {
            return $this->error('Contractor not found.', [], 404);
        }

        $deleted = $this->contractorRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete contractor.');
        }

        AuthService::logActivity(get_current_user_id(), 'CONTRACTOR_DELETE', "Soft deleted contractor ID: $id ($contractor[contractor_code])");

        return $this->success('Contractor deleted successfully.');
    }
}
