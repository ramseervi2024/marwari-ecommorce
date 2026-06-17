<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Repositories\ChallanRepository;
use TransportManagementApi\Services\AuthService;
use WP_REST_Request;

class ChallanController extends BaseController {
    private $challanRepository;

    public function __construct() {
        $this->challanRepository = new ChallanRepository();
    }

    /**
     * GET /challans
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'vehicle_id', 'driver_id', 'challan_amount', 'challan_date', 'payment_status', 'created_at'];
        $search_fields = ['challan_number', 'challan_type', 'remarks'];
        
        $extra_filters = [];
        if (isset($params['vehicle_id'])) {
            $extra_filters['vehicle_id'] = intval($params['vehicle_id']);
        }
        if (isset($params['driver_id'])) {
            $extra_filters['driver_id'] = intval($params['driver_id']);
        }
        if (isset($params['payment_status'])) {
            $extra_filters['payment_status'] = sanitize_text_field($params['payment_status']);
        }

        $results = $this->challanRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Challans retrieved successfully.', $results);
    }

    /**
     * GET /challans/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $challan = $this->challanRepository->findById($id);

        if (!$challan) {
            return $this->error('Challan not found.', [], 404);
        }

        return $this->success('Challan retrieved successfully.', $challan);
    }

    /**
     * POST /challans
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['vehicle_id']) || empty($params['driver_id']) || empty($params['challan_number']) || empty($params['challan_amount'])) {
            return $this->error('Validation failed: vehicle_id, driver_id, challan_number, and challan_amount are required.');
        }

        $number = sanitize_text_field($params['challan_number']);
        if ($this->challanRepository->existsChallanNumber($number)) {
            return $this->error('Validation failed: Challan number already exists.');
        }

        $data = [
            'vehicle_id' => intval($params['vehicle_id']),
            'driver_id' => intval($params['driver_id']),
            'challan_number' => $number,
            'challan_type' => sanitize_text_field($params['challan_type'] ?? 'Traffic Violation'),
            'challan_amount' => floatval($params['challan_amount']),
            'challan_date' => !empty($params['challan_date']) ? sanitize_text_field($params['challan_date']) : date('Y-m-d'),
            'payment_status' => sanitize_text_field($params['payment_status'] ?? 'Pending'),
            'remarks' => sanitize_text_field($params['remarks'] ?? '')
        ];

        $formats = ['%d', '%d', '%s', '%s', '%f', '%s', '%s', '%s'];
        $inserted_id = $this->challanRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to log challan.');
        }

        AuthService::logActivity(get_current_user_id(), 'CHALLAN_CREATE', "Logged fine challan $number costing ₹{$data['challan_amount']} for driver ID {$data['driver_id']}");

        return $this->success('Challan logged successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /challans/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $challan = $this->challanRepository->findById($id);

        if (!$challan) {
            return $this->error('Challan not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = [
            'vehicle_id' => '%d',
            'driver_id' => '%d',
            'challan_number' => '%s',
            'challan_type' => '%s',
            'challan_amount' => '%f',
            'challan_date' => '%s',
            'payment_status' => '%s',
            'remarks' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($field === 'challan_number') {
                    $number = sanitize_text_field($params[$field]);
                    if ($this->challanRepository->existsChallanNumber($number, $id)) {
                        return $this->error('Validation failed: Challan number already exists.');
                    }
                    $data[$field] = $number;
                } elseif ($format === '%d') {
                    $data[$field] = intval($params[$field]);
                } elseif ($format === '%f') {
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

        $updated = $this->challanRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update challan.');
        }

        AuthService::logActivity(get_current_user_id(), 'CHALLAN_UPDATE', "Updated challan record ID: $id");

        return $this->success('Challan updated successfully.', $this->challanRepository->findById($id));
    }

    /**
     * DELETE /challans/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $challan = $this->challanRepository->findById($id);

        if (!$challan) {
            return $this->error('Challan not found.', [], 404);
        }

        $deleted = $this->challanRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete challan.');
        }

        AuthService::logActivity(get_current_user_id(), 'CHALLAN_DELETE', "Soft deleted challan ID: $id ($challan[challan_number])");

        return $this->success('Challan deleted successfully.');
    }
}
