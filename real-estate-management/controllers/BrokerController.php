<?php
namespace RealEstateManagementApi\Controllers;

use RealEstateManagementApi\Repositories\BrokerRepository;
use RealEstateManagementApi\Services\AuthService;
use WP_REST_Request;

class BrokerController extends BaseController {
    private $brokerRepository;

    public function __construct() {
        $this->brokerRepository = new BrokerRepository();
    }

    /**
     * GET /brokers
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'broker_code', 'broker_name', 'commission_percentage', 'status', 'created_at'];
        $search_fields = ['broker_code', 'broker_name', 'mobile', 'email', 'rera_number'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->brokerRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Brokers retrieved successfully.', $results);
    }

    /**
     * GET /brokers/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $broker = $this->brokerRepository->findById($id);

        if (!$broker) {
            return $this->error('Broker not found.', [], 404);
        }

        return $this->success('Broker retrieved successfully.', $broker);
    }

    /**
     * POST /brokers
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['broker_name'])) {
            return $this->error('Validation failed: broker_name is required.');
        }

        // Generate broker code
        $broker_code = 'BRK-RE-' . sprintf('%04d', rand(1000, 9999));
        while ($this->brokerRepository->existsBrokerCode($broker_code)) {
            $broker_code = 'BRK-RE-' . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'broker_code' => $broker_code,
            'broker_name' => sanitize_text_field($params['broker_name']),
            'mobile' => sanitize_text_field($params['mobile'] ?? ''),
            'email' => sanitize_email($params['email'] ?? ''),
            'rera_number' => sanitize_text_field($params['rera_number'] ?? ''),
            'address' => sanitize_textarea_field($params['address'] ?? ''),
            'commission_percentage' => isset($params['commission_percentage']) ? floatval($params['commission_percentage']) : 2.00,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s'];
        $inserted_id = $this->brokerRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to register broker.');
        }

        AuthService::logActivity(get_current_user_id(), 'BROKER_CREATE', "Registered broker $broker_code ($inserted_id)");

        return $this->success('Broker registered successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /brokers/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $broker = $this->brokerRepository->findById($id);

        if (!$broker) {
            return $this->error('Broker not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['broker_name', 'mobile', 'email', 'rera_number', 'address', 'commission_percentage', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'commission_percentage') {
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

        $updated = $this->brokerRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update broker details.');
        }

        AuthService::logActivity(get_current_user_id(), 'BROKER_UPDATE', "Updated broker ID: $id");

        return $this->success('Broker details updated successfully.', $this->brokerRepository->findById($id));
    }

    /**
     * DELETE /brokers/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $broker = $this->brokerRepository->findById($id);

        if (!$broker) {
            return $this->error('Broker not found.', [], 404);
        }

        $deleted = $this->brokerRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete broker.');
        }

        AuthService::logActivity(get_current_user_id(), 'BROKER_DELETE', "Soft deleted broker ID: $id ($broker[broker_code])");

        return $this->success('Broker deleted successfully.');
    }
}
