<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Repositories\SalaryRepository;
use TransportManagementApi\Repositories\DriverRepository;
use TransportManagementApi\Services\AuthService;
use WP_REST_Request;

class SalaryController extends BaseController {
    private $salaryRepository;
    private $driverRepository;

    public function __construct() {
        $this->salaryRepository = new SalaryRepository();
        $this->driverRepository = new DriverRepository();
    }

    /**
     * GET /driver-salary
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'driver_id', 'salary_month', 'total_salary', 'payment_status', 'created_at'];
        $search_fields = ['salary_month'];
        
        $extra_filters = [];
        if (isset($params['driver_id'])) {
            $extra_filters['driver_id'] = intval($params['driver_id']);
        }
        if (isset($params['payment_status'])) {
            $extra_filters['payment_status'] = sanitize_text_field($params['payment_status']);
        }

        $results = $this->salaryRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Driver salaries retrieved successfully.', $results);
    }

    /**
     * GET /driver-salary/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $salary = $this->salaryRepository->findById($id);

        if (!$salary) {
            return $this->error('Salary record not found.', [], 404);
        }

        return $this->success('Salary record retrieved successfully.', $salary);
    }

    /**
     * POST /driver-salary
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['driver_id']) || empty($params['salary_month'])) {
            return $this->error('Validation failed: driver_id and salary_month are required.');
        }

        $driver_id = intval($params['driver_id']);
        $month = sanitize_text_field($params['salary_month']); // format: YYYY-MM
        
        if ($this->salaryRepository->existsSalaryMonth($driver_id, $month)) {
            return $this->error("Validation failed: Salary for driver ID $driver_id already calculated for month $month.");
        }

        $driver = $this->driverRepository->findById($driver_id);
        if (!$driver) {
            return $this->error('Driver not found.');
        }

        // Auto calculate trips completed by this driver for this month to compute per trip salary if applicable
        global $wpdb;
        $table_trips = $wpdb->prefix . 'transport_trips';
        $start_date = $month . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        $trips_count = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_trips WHERE driver_id = %d AND status = 'Delivered' AND trip_end_date BETWEEN %s AND %s",
            $driver_id, $start_date, $end_date
        ));

        $fixed_salary = floatval($driver['fixed_salary']);
        $per_trip = floatval($driver['per_trip_salary']);
        $allowance = floatval($params['allowance'] ?? 0.00);
        $deduction = floatval($params['deduction'] ?? 0.00);

        // Trip bonus or per trip salary based on salary type
        $trip_bonus = 0.00;
        if ($driver['salary_type'] === 'per_trip') {
            $trip_bonus = $per_trip * $trips_count;
            $fixed_salary = 0.00; // No fixed salary
        } else {
            // Give flat bonus of 500 per completed trip as incentive
            $trip_bonus = 500.00 * $trips_count;
        }

        if (isset($params['trip_bonus'])) {
            $trip_bonus = floatval($params['trip_bonus']);
        }
        if (isset($params['fixed_salary'])) {
            $fixed_salary = floatval($params['fixed_salary']);
        }

        $total = $fixed_salary + $trip_bonus + $allowance - $deduction;

        $data = [
            'driver_id' => $driver_id,
            'salary_month' => $month,
            'fixed_salary' => $fixed_salary,
            'trip_bonus' => $trip_bonus,
            'allowance' => $allowance,
            'deduction' => $deduction,
            'total_salary' => $total,
            'payment_status' => sanitize_text_field($params['payment_status'] ?? 'Pending')
        ];

        $formats = ['%d', '%s', '%f', '%f', '%f', '%f', '%f', '%s'];
        $inserted_id = $this->salaryRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to log salary record.');
        }

        AuthService::logActivity(get_current_user_id(), 'SALARY_CREATE', "Calculated salary of ₹{$total} for driver ID {$driver_id} for month {$month}");

        return $this->success('Salary calculated and logged successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /driver-salary/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $salary = $this->salaryRepository->findById($id);

        if (!$salary) {
            return $this->error('Salary record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = [
            'fixed_salary' => '%f',
            'trip_bonus' => '%f',
            'allowance' => '%f',
            'deduction' => '%f',
            'payment_status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%f') {
                    $data[$field] = floatval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        // Recalculate total salary if any math-based fields changed
        $fixed = isset($data['fixed_salary']) ? $data['fixed_salary'] : floatval($salary['fixed_salary']);
        $bonus = isset($data['trip_bonus']) ? $data['trip_bonus'] : floatval($salary['trip_bonus']);
        $allow = isset($data['allowance']) ? $data['allowance'] : floatval($salary['allowance']);
        $deduct = isset($data['deduction']) ? $data['deduction'] : floatval($salary['deduction']);
        
        $data['total_salary'] = $fixed + $bonus + $allow - $deduct;
        $formats[] = '%f';

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->salaryRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update salary record.');
        }

        AuthService::logActivity(get_current_user_id(), 'SALARY_UPDATE', "Updated driver salary log ID: $id status: " . ($data['payment_status'] ?? 'N/A'));

        return $this->success('Salary record updated successfully.', $this->salaryRepository->findById($id));
    }

    /**
     * DELETE /driver-salary/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $salary = $this->salaryRepository->findById($id);

        if (!$salary) {
            return $this->error('Salary record not found.', [], 404);
        }

        $deleted = $this->salaryRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete salary record.');
        }

        AuthService::logActivity(get_current_user_id(), 'SALARY_DELETE', "Soft deleted salary record ID: $id");

        return $this->success('Salary record deleted successfully.');
    }
}
