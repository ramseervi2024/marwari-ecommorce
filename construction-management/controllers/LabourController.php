<?php
namespace ConstructionManagementApi\Controllers;

use ConstructionManagementApi\Repositories\LabourRepository;
use ConstructionManagementApi\Repositories\AttendanceRepository;
use ConstructionManagementApi\Repositories\PayrollRepository;
use ConstructionManagementApi\Services\AuthService;
use WP_REST_Request;

class LabourController extends BaseController {
    private $labourRepository;
    private $attendanceRepository;
    private $payrollRepository;

    public function __construct() {
        $this->labourRepository = new LabourRepository();
        $this->attendanceRepository = new AttendanceRepository();
        $this->payrollRepository = new PayrollRepository();
    }

    // --- LABOUR ACTIONS ---

    /**
     * GET /labours
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'employee_code', 'name', 'trade', 'daily_wage', 'attendance_status'];
        $search_fields = ['employee_code', 'name', 'mobile', 'trade', 'attendance_status'];

        $extra_filters = [];
        if (isset($params['project_id'])) {
            $extra_filters['project_id'] = intval($params['project_id']);
        }
        if (isset($params['trade'])) {
            $extra_filters['trade'] = sanitize_text_field($params['trade']);
        }
        if (isset($params['attendance_status'])) {
            $extra_filters['attendance_status'] = sanitize_text_field($params['attendance_status']);
        }

        $results = $this->labourRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Labour workers list retrieved successfully.', $results);
    }

    /**
     * GET /labours/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $labour = $this->labourRepository->findById($id);

        if (!$labour) {
            return $this->error('Labour worker profile not found.', [], 404);
        }

        return $this->success('Labour worker profile retrieved successfully.', $labour);
    }

    /**
     * POST /labours
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['name']) || empty($params['trade']) || !isset($params['daily_wage'])) {
            return $this->error('Validation failed: name, trade, and daily_wage are required.');
        }

        $employee_code = 'LAB-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        while ($this->labourRepository->existsEmployeeCode($employee_code)) {
            $employee_code = 'LAB-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'employee_code' => $employee_code,
            'name' => sanitize_text_field($params['name']),
            'mobile' => sanitize_text_field($params['mobile'] ?? ''),
            'trade' => sanitize_text_field($params['trade']),
            'daily_wage' => floatval($params['daily_wage']),
            'attendance_status' => sanitize_text_field($params['attendance_status'] ?? 'ABSENT'),
            'project_id' => !empty($params['project_id']) ? intval($params['project_id']) : null
        ];

        $formats = ['%s', '%s', '%s', '%s', '%f', '%s', '%d'];
        $inserted_id = $this->labourRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create labour worker profile.');
        }

        AuthService::logActivity(get_current_user_id(), 'LABOUR_CREATE', "Created labour worker profile code $employee_code ($inserted_id)");

        return $this->success('Labour worker profile created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /labours/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $labour = $this->labourRepository->findById($id);

        if (!$labour) {
            return $this->error('Labour worker profile not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['name', 'mobile', 'trade', 'daily_wage', 'attendance_status', 'project_id'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'daily_wage') {
                    $data[$field] = floatval($params[$field]);
                    $formats[] = '%f';
                } elseif ($field === 'project_id') {
                    $data[$field] = intval($params[$field]);
                    $formats[] = '%d';
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                    $formats[] = '%s';
                }
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->labourRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update labour worker profile.');
        }

        AuthService::logActivity(get_current_user_id(), 'LABOUR_UPDATE', "Updated labour worker record ID: $id");

        return $this->success('Labour worker profile updated successfully.', $this->labourRepository->findById($id));
    }

    /**
     * DELETE /labours/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $labour = $this->labourRepository->findById($id);

        if (!$labour) {
            return $this->error('Labour worker profile not found.', [], 404);
        }

        $deleted = $this->labourRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete labour worker profile.');
        }

        AuthService::logActivity(get_current_user_id(), 'LABOUR_DELETE', "Soft deleted worker ID: $id ($labour[employee_code])");

        return $this->success('Labour worker profile deleted successfully.');
    }

    // --- ATTENDANCE ACTIONS ---

    /**
     * GET /attendance
     */
    public function getAllAttendance(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'labour_id', 'project_id', 'attendance_date', 'status', 'working_hours'];
        
        $extra_filters = [];
        if (isset($params['labour_id'])) {
            $extra_filters['labour_id'] = intval($params['labour_id']);
        }
        if (isset($params['project_id'])) {
            $extra_filters['project_id'] = intval($params['project_id']);
        }
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['attendance_date'])) {
            $extra_filters['attendance_date'] = sanitize_text_field($params['attendance_date']);
        }

        $results = $this->attendanceRepository->findAll($params, $allowed_sorts, ['status'], $extra_filters);
        return $this->success('Attendance logs retrieved successfully.', $results);
    }

    /**
     * POST /attendance
     */
    public function createAttendance(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['labour_id']) || empty($params['project_id']) || empty($params['attendance_date'])) {
            return $this->error('Validation failed: labour_id, project_id, and attendance_date are required.');
        }

        $status = sanitize_text_field($params['status'] ?? 'Present');
        $data = [
            'labour_id' => intval($params['labour_id']),
            'project_id' => intval($params['project_id']),
            'attendance_date' => sanitize_text_field($params['attendance_date']),
            'status' => $status,
            'working_hours' => isset($params['working_hours']) ? floatval($params['working_hours']) : 8.00,
            'overtime_hours' => isset($params['overtime_hours']) ? floatval($params['overtime_hours']) : 0.00
        ];

        $formats = ['%d', '%d', '%s', '%s', '%f', '%f'];
        $inserted_id = $this->attendanceRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to log attendance.');
        }

        // Proactively update worker's status
        $worker_status = (strtoupper($status) === 'PRESENT') ? 'PRESENT' : 'ABSENT';
        $this->labourRepository->update($data['labour_id'], ['attendance_status' => $worker_status], ['%s']);

        AuthService::logActivity(get_current_user_id(), 'ATTENDANCE_LOG', "Logged attendance status: $status for worker ID: $params[labour_id]");

        return $this->success('Attendance logged successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /attendance/:id
     */
    public function updateAttendance(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $attendance = $this->attendanceRepository->findById($id);

        if (!$attendance) {
            return $this->error('Attendance log not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['status', 'working_hours', 'overtime_hours', 'attendance_date'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'working_hours' || $field === 'overtime_hours') {
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

        $updated = $this->attendanceRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update attendance log.');
        }

        // Update worker status if status changed
        if (isset($data['status'])) {
            $worker_status = (strtoupper($data['status']) === 'PRESENT') ? 'PRESENT' : 'ABSENT';
            $this->labourRepository->update($attendance['labour_id'], ['attendance_status' => $worker_status], ['%s']);
        }

        AuthService::logActivity(get_current_user_id(), 'ATTENDANCE_UPDATE', "Updated attendance log ID: $id");

        return $this->success('Attendance log updated successfully.', $this->attendanceRepository->findById($id));
    }

    /**
     * DELETE /attendance/:id
     */
    public function deleteAttendance(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $attendance = $this->attendanceRepository->findById($id);

        if (!$attendance) {
            return $this->error('Attendance log not found.', [], 404);
        }

        $deleted = $this->attendanceRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete attendance log.');
        }

        AuthService::logActivity(get_current_user_id(), 'ATTENDANCE_DELETE', "Soft deleted attendance log ID: $id");

        return $this->success('Attendance log deleted successfully.');
    }

    // --- PAYROLL ACTIONS ---

    /**
     * GET /payroll
     */
    public function getAllPayroll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'labour_id', 'project_id', 'start_date', 'end_date', 'total_earnings', 'payment_status'];

        $extra_filters = [];
        if (isset($params['labour_id'])) {
            $extra_filters['labour_id'] = intval($params['labour_id']);
        }
        if (isset($params['project_id'])) {
            $extra_filters['project_id'] = intval($params['project_id']);
        }
        if (isset($params['payment_status'])) {
            $extra_filters['payment_status'] = sanitize_text_field($params['payment_status']);
        }

        $results = $this->payrollRepository->findAll($params, $allowed_sorts, ['payment_status'], $extra_filters);
        return $this->success('Payroll slips retrieved successfully.', $results);
    }

    /**
     * POST /payroll (Generates & Calculates payroll slip automatically from attendance records)
     */
    public function createPayroll(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_json_params();

        if (empty($params['labour_id']) || empty($params['project_id']) || empty($params['start_date']) || empty($params['end_date'])) {
            return $this->error('Validation failed: labour_id, project_id, start_date, and end_date are required.');
        }

        $labour_id = intval($params['labour_id']);
        $project_id = intval($params['project_id']);
        $start_date = sanitize_text_field($params['start_date']);
        $end_date = sanitize_text_field($params['end_date']);

        $worker = $this->labourRepository->findById($labour_id);
        if (!$worker) {
            return $this->error('Labour worker not found.');
        }

        $daily_wage = floatval($worker['daily_wage']);

        // Query all attendance entries for this worker in this range
        $table_attendance = $wpdb->prefix . 'construction_attendance';
        $attendances = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_attendance 
             WHERE labour_id = %d AND project_id = %d 
               AND attendance_date BETWEEN %s AND %s 
               AND deleted_at IS NULL",
            $labour_id, $project_id, $start_date, $end_date
        ), ARRAY_A);

        $total_days = 0;
        $regular_hours = 0.00;
        $overtime_hours = 0.00;

        foreach ($attendances as $att) {
            $status = strtolower($att['status']);
            if ($status === 'present') {
                $total_days += 1;
                $regular_hours += floatval($att['working_hours']);
                $overtime_hours += floatval($att['overtime_hours']);
            } elseif ($status === 'half day') {
                $total_days += 0.5;
                $regular_hours += floatval($att['working_hours']);
                $overtime_hours += floatval($att['overtime_hours']);
            }
        }

        $regular_earnings = $total_days * $daily_wage;
        // Overtime rate: 1.5x hourly wage (assuming daily_wage / 8 is the hourly wage)
        $hourly_wage = $daily_wage / 8.00;
        $overtime_earnings = $overtime_hours * $hourly_wage * 1.5;
        $total_earnings = $regular_earnings + $overtime_earnings;

        $data = [
            'labour_id' => $labour_id,
            'project_id' => $project_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_days_worked' => $total_days,
            'regular_earnings' => $regular_earnings,
            'overtime_earnings' => $overtime_earnings,
            'total_earnings' => $total_earnings,
            'payment_status' => sanitize_text_field($params['payment_status'] ?? 'Unpaid'),
            'payment_date' => !empty($params['payment_date']) ? sanitize_text_field($params['payment_date']) : null
        ];

        $formats = ['%d', '%d', '%s', '%s', '%f', '%f', '%f', '%f', '%s', '%s'];
        $inserted_id = $this->payrollRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create payroll slip.');
        }

        AuthService::logActivity(get_current_user_id(), 'PAYROLL_CREATE', "Calculated payroll ID: $inserted_id for worker ID: $labour_id total: $total_earnings");

        return $this->success('Payroll slip generated and calculated successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /payroll/:id
     */
    public function updatePayroll(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $payroll = $this->payrollRepository->findById($id);

        if (!$payroll) {
            return $this->error('Payroll slip not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['payment_status', 'payment_date'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                $data[$field] = sanitize_text_field($params[$field]);
                $formats[] = '%s';
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->payrollRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update payroll slip.');
        }

        AuthService::logActivity(get_current_user_id(), 'PAYROLL_UPDATE', "Updated payroll slip ID: $id");

        return $this->success('Payroll slip updated successfully.', $this->payrollRepository->findById($id));
    }

    /**
     * DELETE /payroll/:id
     */
    public function deletePayroll(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $payroll = $this->payrollRepository->findById($id);

        if (!$payroll) {
            return $this->error('Payroll slip not found.', [], 404);
        }

        $deleted = $this->payrollRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete payroll slip.');
        }

        AuthService::logActivity(get_current_user_id(), 'PAYROLL_DELETE', "Soft deleted payroll ID: $id");

        return $this->success('Payroll slip deleted successfully.');
    }
}
