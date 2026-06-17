<?php
namespace ServiceManagementApi\Controllers;

use ServiceManagementApi\Repositories\AmcRepository;
use ServiceManagementApi\Services\AuthService;
use WP_REST_Request;

class AmcController extends BaseController {
    private $amcRepository;

    public function __construct() {
        $this->amcRepository = new AmcRepository();
    }

    /**
     * GET /amc
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'contract_number', 'start_date', 'end_date', 'total_amount', 'status'];
        $search_fields = ['contract_number', 'customer_name', 'email', 'phone', 'status'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->amcRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('AMC contract list retrieved.', $results);
    }

    /**
     * GET /amc/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $amc = $this->amcRepository->findById($id);

        if (!$amc) {
            return $this->error('AMC contract not found.', [], 404);
        }

        return $this->success('AMC contract details retrieved.', $amc);
    }

    /**
     * POST /amc
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['customer_name']) || empty($params['start_date']) || empty($params['end_date']) || empty($params['total_amount'])) {
            return $this->error('customer_name, start_date, end_date, and total_amount are required.');
        }

        // Generate contract number
        $contract_number = 'AMC-' . date('Y') . '-' . sprintf('%04d', rand(1, 9999));
        while ($this->amcRepository->existsContractNumber($contract_number)) {
            $contract_number = 'AMC-' . date('Y') . '-' . sprintf('%04d', rand(1, 9999));
        }

        $data = [
            'contract_number' => $contract_number,
            'customer_name' => sanitize_text_field($params['customer_name']),
            'email' => sanitize_email($params['email'] ?? ''),
            'phone' => sanitize_text_field($params['phone'] ?? ''),
            'start_date' => sanitize_text_field($params['start_date']),
            'end_date' => sanitize_text_field($params['end_date']),
            'total_amount' => floatval($params['total_amount']),
            'status' => sanitize_text_field($params['status'] ?? 'Active')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s'];
        $inserted_id = $this->amcRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create AMC contract.');
        }

        AuthService::logActivity(
            get_current_user_id(),
            'AMC_CREATE',
            "Created AMC contract $contract_number (Value: {$data['total_amount']}) for {$data['customer_name']}"
        );

        return $this->success('AMC contract created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /amc/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $amc = $this->amcRepository->findById($id);

        if (!$amc) {
            return $this->error('AMC contract not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'customer_name' => '%s',
            'email' => '%s',
            'phone' => '%s',
            'start_date' => '%s',
            'end_date' => '%s',
            'total_amount' => '%f',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($field === 'email') {
                    $data[$field] = sanitize_email($params[$field]);
                } elseif ($field === 'total_amount') {
                    $data[$field] = floatval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->amcRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update AMC contract.');
        }

        AuthService::logActivity(get_current_user_id(), 'AMC_UPDATE', "Updated AMC contract ID: $id");

        return $this->success('AMC contract updated successfully.', $this->amcRepository->findById($id));
    }

    /**
     * DELETE /amc/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $amc = $this->amcRepository->findById($id);

        if (!$amc) {
            return $this->error('AMC contract not found.', [], 404);
        }

        $deleted = $this->amcRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete AMC contract.');
        }

        AuthService::logActivity(get_current_user_id(), 'AMC_DELETE', "Soft deleted AMC Contract ID: $id ({$amc['contract_number']})");

        return $this->success('AMC contract deleted successfully.');
    }
}
