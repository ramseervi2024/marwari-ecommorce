<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\ClientRepository;
use WorkspaceErpApi\Services\AuthService;
use WP_REST_Request;

class ClientController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new ClientRepository();
    }

    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $result = $this->repository->findAll($params, ['id', 'client_code', 'company_name', 'status'], ['client_code', 'company_name', 'contact_person', 'email']);
        return $this->success('Clients fetched successfully', $result);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['company_name']) || empty($params['client_code'])) {
            return $this->error('Validation failed: company_name and client_code are required.');
        }

        $code = sanitize_text_field($params['client_code']);
        if ($this->repository->existsClientCode($code)) {
            return $this->error("Client code '$code' already exists.");
        }

        $data = [
            'client_code' => $code,
            'company_name' => sanitize_text_field($params['company_name']),
            'industry' => isset($params['industry']) ? sanitize_text_field($params['industry']) : '',
            'contact_person' => isset($params['contact_person']) ? sanitize_text_field($params['contact_person']) : '',
            'email' => isset($params['email']) ? sanitize_email($params['email']) : '',
            'mobile' => isset($params['mobile']) ? sanitize_text_field($params['mobile']) : '',
            'gst_number' => isset($params['gst_number']) ? sanitize_text_field($params['gst_number']) : '',
            'address' => isset($params['address']) ? sanitize_textarea_field($params['address']) : '',
            'city' => isset($params['city']) ? sanitize_text_field($params['city']) : 'Bangalore',
            'state' => isset($params['state']) ? sanitize_text_field($params['state']) : 'Karnataka',
            'contract_start' => isset($params['contract_start']) ? sanitize_text_field($params['contract_start']) : null,
            'contract_end' => isset($params['contract_end']) ? sanitize_text_field($params['contract_end']) : null,
            'total_seats' => isset($params['total_seats']) ? intval($params['total_seats']) : 0,
            'monthly_rent' => isset($params['monthly_rent']) ? floatval($params['monthly_rent']) : 0.00,
            'security_deposit' => isset($params['security_deposit']) ? floatval($params['security_deposit']) : 0.00,
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%f', '%f', '%s', '%s', '%s']);
        if (!$id) return $this->error('Failed to create client.');

        AuthService::logActivity(get_current_user_id(), 'CREATE_CLIENT', "Created client $code (ID: $id)");
        return $this->success('Client created successfully', array_merge(['id' => $id], $data), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $client = $this->repository->findById($id);
        if (!$client) return $this->error('Client not found.', [], 404);

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

        $success = $this->repository->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update client.');

        AuthService::logActivity(get_current_user_id(), 'UPDATE_CLIENT', "Updated client details ID: $id");
        return $this->success('Client updated successfully', $this->repository->findById($id));
    }

    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $client = $this->repository->findById($id);
        if (!$client) return $this->error('Client not found.', [], 404);

        $this->repository->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DELETE_CLIENT', "Soft deleted client ID: $id");
        return $this->success('Client deleted successfully');
    }
}
