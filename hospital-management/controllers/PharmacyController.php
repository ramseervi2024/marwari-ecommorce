<?php
namespace HospitalManagementApi\Controllers;

use HospitalManagementApi\Repositories\PharmacyRepository;
use HospitalManagementApi\Services\AuthService;
use WP_REST_Request;

class PharmacyController extends BaseController {
    private $pharmacyRepository;

    public function __construct() {
        $this->pharmacyRepository = new PharmacyRepository();
    }

    /**
     * GET /pharmacy
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'medicine_name', 'quantity', 'selling_price', 'expiry_date', 'status'];
        $search_fields = ['medicine_name', 'batch_number', 'manufacturer'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->pharmacyRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Pharmacy records retrieved successfully.', $results);
    }

    /**
     * GET /pharmacy/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $medicine = $this->pharmacyRepository->findById($id);

        if (!$medicine) {
            return $this->error('Medicine not found in inventory.', [], 404);
        }

        return $this->success('Medicine record retrieved successfully.', $medicine);
    }

    /**
     * POST /pharmacy
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['medicine_name']) || empty($params['batch_number']) || empty($params['expiry_date'])) {
            return $this->error('Validation failed: medicine_name, batch_number, and expiry_date are required.');
        }

        $data = [
            'medicine_name' => sanitize_text_field($params['medicine_name']),
            'batch_number' => sanitize_text_field($params['batch_number']),
            'manufacturer' => sanitize_text_field($params['manufacturer'] ?? ''),
            'purchase_price' => floatval($params['purchase_price'] ?? 0.00),
            'selling_price' => floatval($params['selling_price'] ?? 0.00),
            'quantity' => intval($params['quantity'] ?? 0),
            'expiry_date' => sanitize_text_field($params['expiry_date']),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%f', '%f', '%d', '%s', '%s'];
        $inserted_id = $this->pharmacyRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to add medicine to inventory.');
        }

        AuthService::logActivity(get_current_user_id(), 'MEDICINE_CREATE', "Added medicine to pharmacy: $params[medicine_name] ($inserted_id)");

        return $this->success('Medicine added successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /pharmacy/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $medicine = $this->pharmacyRepository->findById($id);

        if (!$medicine) {
            return $this->error('Medicine not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $string_fields = ['medicine_name', 'batch_number', 'manufacturer', 'expiry_date', 'status'];
        foreach ($string_fields as $field) {
            if (isset($params[$field])) {
                $data[$field] = sanitize_text_field($params[$field]);
                $formats[] = '%s';
            }
        }

        if (isset($params['purchase_price'])) {
            $data['purchase_price'] = floatval($params['purchase_price']);
            $formats[] = '%f';
        }
        if (isset($params['selling_price'])) {
            $data['selling_price'] = floatval($params['selling_price']);
            $formats[] = '%f';
        }
        if (isset($params['quantity'])) {
            $data['quantity'] = intval($params['quantity']);
            $formats[] = '%d';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->pharmacyRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update medicine record.');
        }

        AuthService::logActivity(get_current_user_id(), 'MEDICINE_UPDATE', "Updated medicine record ID: $id");

        return $this->success('Medicine record updated successfully.', $this->pharmacyRepository->findById($id));
    }

    /**
     * DELETE /pharmacy/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $medicine = $this->pharmacyRepository->findById($id);

        if (!$medicine) {
            return $this->error('Medicine not found.', [], 404);
        }

        $deleted = $this->pharmacyRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete medicine record.');
        }

        AuthService::logActivity(get_current_user_id(), 'MEDICINE_DELETE', "Soft deleted medicine ID: $id");

        return $this->success('Medicine record deleted successfully.');
    }
}
