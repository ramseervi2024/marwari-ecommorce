<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Repositories\FuelRepository;
use TransportManagementApi\Services\AuthService;
use WP_REST_Request;

class FuelController extends BaseController {
    private $fuelRepository;

    public function __construct() {
        $this->fuelRepository = new FuelRepository();
    }

    /**
     * GET /fuel
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'vehicle_id', 'trip_id', 'total_cost', 'fuel_date', 'created_at'];
        $search_fields = ['fuel_station'];
        
        $extra_filters = [];
        if (isset($params['vehicle_id'])) {
            $extra_filters['vehicle_id'] = intval($params['vehicle_id']);
        }
        if (isset($params['trip_id'])) {
            $extra_filters['trip_id'] = intval($params['trip_id']);
        }

        $results = $this->fuelRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Fuel records retrieved successfully.', $results);
    }

    /**
     * GET /fuel/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $fuel = $this->fuelRepository->findById($id);

        if (!$fuel) {
            return $this->error('Fuel record not found.', [], 404);
        }

        return $this->success('Fuel record retrieved successfully.', $fuel);
    }

    /**
     * POST /fuel
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['vehicle_id']) || empty($params['fuel_quantity']) || empty($params['rate_per_liter'])) {
            return $this->error('Validation failed: vehicle_id, fuel_quantity, and rate_per_liter are required.');
        }

        $qty = floatval($params['fuel_quantity']);
        $rate = floatval($params['rate_per_liter']);
        $total = $qty * $rate;

        $data = [
            'vehicle_id' => intval($params['vehicle_id']),
            'trip_id' => !empty($params['trip_id']) ? intval($params['trip_id']) : null,
            'fuel_station' => sanitize_text_field($params['fuel_station'] ?? ''),
            'fuel_quantity' => $qty,
            'rate_per_liter' => $rate,
            'total_cost' => $total,
            'odometer_reading' => intval($params['odometer_reading'] ?? 0),
            'fuel_date' => !empty($params['fuel_date']) ? sanitize_text_field($params['fuel_date']) : date('Y-m-d')
        ];

        $formats = ['%d', '%d', '%s', '%f', '%f', '%f', '%d', '%s'];
        $inserted_id = $this->fuelRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to log fuel record.');
        }

        // Also insert into global expenses table if trip is linked
        if ($data['trip_id']) {
            global $wpdb;
            $wpdb->insert($wpdb->prefix . 'transport_expenses', [
                'trip_id' => $data['trip_id'],
                'expense_type' => 'Fuel',
                'amount' => $total,
                'expense_date' => $data['fuel_date'],
                'description' => "Auto-logged fuel expense: {$qty}L @ ₹{$rate}/L from {$data['fuel_station']}"
            ]);
        }

        AuthService::logActivity(get_current_user_id(), 'FUEL_CREATE', "Logged fuel purchase of {$qty}L costing ₹{$total} for vehicle ID {$data['vehicle_id']}");

        return $this->success('Fuel purchase logged successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /fuel/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $fuel = $this->fuelRepository->findById($id);

        if (!$fuel) {
            return $this->error('Fuel record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = [
            'vehicle_id' => '%d',
            'trip_id' => '%d',
            'fuel_station' => '%s',
            'fuel_quantity' => '%f',
            'rate_per_liter' => '%f',
            'odometer_reading' => '%d',
            'fuel_date' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%d') {
                    $data[$field] = intval($params[$field]);
                } elseif ($format === '%f') {
                    $data[$field] = floatval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        // Recalculate total cost if quantity or rate was updated
        $qty = isset($data['fuel_quantity']) ? $data['fuel_quantity'] : floatval($fuel['fuel_quantity']);
        $rate = isset($data['rate_per_liter']) ? $data['rate_per_liter'] : floatval($fuel['rate_per_liter']);
        $data['total_cost'] = $qty * $rate;
        $formats[] = '%f';

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->fuelRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update fuel record.');
        }

        AuthService::logActivity(get_current_user_id(), 'FUEL_UPDATE', "Updated fuel log ID: $id");

        return $this->success('Fuel log updated successfully.', $this->fuelRepository->findById($id));
    }

    /**
     * DELETE /fuel/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $fuel = $this->fuelRepository->findById($id);

        if (!$fuel) {
            return $this->error('Fuel record not found.', [], 404);
        }

        $deleted = $this->fuelRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete fuel record.');
        }

        AuthService::logActivity(get_current_user_id(), 'FUEL_DELETE', "Soft deleted fuel log ID: $id");

        return $this->success('Fuel record deleted successfully.');
    }
}
