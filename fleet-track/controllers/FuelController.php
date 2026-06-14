<?php
namespace FleetTrackPro\Controllers;

use FleetTrackPro\Repositories\FuelRepository;
use FleetTrackPro\Services\AuthService;
use WP_REST_Request;

class FuelController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new FuelRepository();
    }

    /**
     * GET /fuel
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $result = $this->repository->findAllWithDetails($params);
        return $this->success('Fuel records fetched successfully', $result);
    }

    /**
     * POST /fuel
     */
    public function create(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_json_params();

        if (empty($params['vehicle_id']) || !isset($params['fuel_quantity']) || !isset($params['fuel_cost']) || empty($params['fuel_date'])) {
            return $this->error('Validation failed: vehicle_id, fuel_quantity, fuel_cost, fuel_date are required.');
        }

        $qty = (float)$params['fuel_quantity'];
        $cost = (float)$params['fuel_cost'];
        $price = $qty > 0 ? round($cost / $qty, 2) : 0.00;
        
        $data = [
            'vehicle_id' => (int)$params['vehicle_id'],
            'trip_id' => !empty($params['trip_id']) ? (int)$params['trip_id'] : null,
            'fuel_quantity' => $qty,
            'fuel_cost' => $cost,
            'fuel_price_per_liter' => isset($params['fuel_price_per_liter']) ? (float)$params['fuel_price_per_liter'] : $price,
            'fuel_station' => sanitize_text_field($params['fuel_station'] ?? ''),
            'fuel_date' => sanitize_text_field($params['fuel_date']),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%d', '%d', '%f', '%f', '%f', '%s', '%s', '%s', '%s'];
        $fuel_id = $this->repository->create($data, $formats);

        if (!$fuel_id) {
            return $this->error('Failed to register fuel log.');
        }

        // Automatic Cost Integration: Link this fuel cost to the expenses table!
        $table_expenses = $wpdb->prefix . 'fleet_expenses';
        $station_desc = !empty($data['fuel_station']) ? " at " . $data['fuel_station'] : '';
        $desc = "Fuel log ID: $fuel_id. Quantity: $qty L @ $price/L" . $station_desc;
        
        $wpdb->insert(
            $table_expenses,
            [
                'vehicle_id' => $data['vehicle_id'],
                'driver_id' => null, // Unknown driver from direct fuel log
                'trip_id' => $data['trip_id'],
                'expense_type' => 'Fuel',
                'amount' => $cost,
                'expense_date' => $data['fuel_date'],
                'description' => $desc,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ],
            ['%d', '%d', '%d', '%s', '%f', '%s', '%s', '%s', '%s']
        );

        AuthService::logActivity(get_current_user_id(), 'CREATE_FUEL_LOG', "Recorded fuel log ID: $fuel_id (Cost: $cost, Auto-synced to Expenses)");

        return $this->success('Fuel record logged successfully', $this->repository->findFuelWithDetails($fuel_id), 201);
    }

    /**
     * PUT /fuel/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $fuel = $this->repository->findById($id);

        if (!$fuel) {
            return $this->error('Fuel record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $update_data = [];
        $formats = [];

        $allowed_fields = [
            'vehicle_id' => '%d',
            'trip_id' => '%d',
            'fuel_quantity' => '%f',
            'fuel_cost' => '%f',
            'fuel_price_per_liter' => '%f',
            'fuel_station' => '%s',
            'fuel_date' => '%s'
        ];

        foreach ($allowed_fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%d') {
                    $update_data[$field] = (int)$params[$field];
                } elseif ($format === '%f') {
                    $update_data[$field] = (float)$params[$field];
                } else {
                    $update_data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($update_data)) {
            return $this->error('No parameters provided for update.');
        }

        $update_data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->repository->update($id, $update_data, $formats);

        if (!$success) {
            return $this->error('Failed to update fuel log details.');
        }

        AuthService::logActivity(get_current_user_id(), 'UPDATE_FUEL_LOG', "Updated fuel log ID: $id");

        return $this->success('Fuel record updated successfully', $this->repository->findFuelWithDetails($id));
    }

    /**
     * DELETE /fuel/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $fuel = $this->repository->findById($id);

        if (!$fuel) {
            return $this->error('Fuel record not found.', [], 404);
        }

        $success = $this->repository->delete($id);

        if (!$success) {
            return $this->error('Failed to delete fuel record.');
        }

        AuthService::logActivity(get_current_user_id(), 'DELETE_FUEL_LOG', "Soft deleted fuel record ID: $id");

        return $this->success('Fuel record deleted successfully');
    }
}
