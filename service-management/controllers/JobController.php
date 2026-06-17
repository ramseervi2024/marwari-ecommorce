<?php
namespace ServiceManagementApi\Controllers;

use ServiceManagementApi\Repositories\JobRepository;
use ServiceManagementApi\Repositories\QuotationRepository;
use ServiceManagementApi\Services\AuthService;
use WP_REST_Request;

class JobController extends BaseController {
    private $jobRepository;
    private $quotationRepository;

    public function __construct() {
        $this->jobRepository = new JobRepository();
        $this->quotationRepository = new QuotationRepository();
    }

    /**
     * GET /jobs
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'job_number', 'scheduled_date', 'status', 'priority'];
        $search_fields = ['job_number', 'customer_name', 'phone', 'address', 'status', 'priority', 'description', 'work_notes'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['technician_id'])) {
            $extra_filters['technician_id'] = intval($params['technician_id']);
        }

        // Technicians can only view their own assigned jobs
        if (current_user_can('view_assigned_jobs') && !current_user_can('manage_jobs')) {
            $extra_filters['technician_id'] = get_current_user_id();
        }

        $results = $this->jobRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);

        foreach ($results['data'] as &$row) {
            if (!empty($row['technician_id'])) {
                $tech = get_userdata($row['technician_id']);
                $row['technician_name'] = $tech ? $tech->display_name : 'Unknown';
            } else {
                $row['technician_name'] = 'Unassigned';
            }

            if (!empty($row['quotation_id'])) {
                $quote = $this->quotationRepository->findById($row['quotation_id']);
                $row['quotation_number'] = $quote ? $quote['quotation_number'] : '';
            } else {
                $row['quotation_number'] = '';
            }
        }

        return $this->success('Jobs list retrieved.', $results);
    }

    /**
     * GET /jobs/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $job = $this->jobRepository->findById($id);

        if (!$job) {
            return $this->error('Job not found.', [], 404);
        }

        // Technicians can only view their own assigned jobs
        if (current_user_can('view_assigned_jobs') && !current_user_can('manage_jobs')) {
            if (intval($job['technician_id']) !== get_current_user_id()) {
                return $this->error('Access Forbidden: You are not assigned to this job.', [], 403);
            }
        }

        if (!empty($job['technician_id'])) {
            $tech = get_userdata($job['technician_id']);
            $job['technician_name'] = $tech ? $tech->display_name : 'Unknown';
        } else {
            $job['technician_name'] = 'Unassigned';
        }

        if (!empty($job['quotation_id'])) {
            $quote = $this->quotationRepository->findById($job['quotation_id']);
            $job['quotation_number'] = $quote ? $quote['quotation_number'] : '';
            $job['quotation_total'] = $quote ? $quote['total_amount'] : 0.00;
        } else {
            $job['quotation_number'] = '';
            $job['quotation_total'] = 0.00;
        }

        return $this->success('Job retrieved successfully.', $job);
    }

    /**
     * POST /jobs
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['customer_name']) || empty($params['phone']) || empty($params['address'])) {
            return $this->error('customer_name, phone, and address are required.');
        }

        // Generate job number
        $job_number = 'JOB-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        while ($this->jobRepository->existsJobNumber($job_number)) {
            $job_number = 'JOB-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        }

        $scheduled_date = sanitize_text_field($params['scheduled_date'] ?? date('Y-m-d'));
        $technician_id = isset($params['technician_id']) ? intval($params['technician_id']) : null;
        $quotation_id = isset($params['quotation_id']) ? intval($params['quotation_id']) : null;

        if ($technician_id) {
            $tech = get_userdata($technician_id);
            if (!$tech || !in_array('service_technician', $tech->roles) && !in_array('service_super_admin', $tech->roles) && !in_array('service_manager', $tech->roles) && !in_array('administrator', $tech->roles)) {
                return $this->error('Selected user is not registered as a technician.');
            }
        }

        $data = [
            'job_number' => $job_number,
            'customer_name' => sanitize_text_field($params['customer_name']),
            'phone' => sanitize_text_field($params['phone']),
            'address' => sanitize_textarea_field($params['address']),
            'technician_id' => $technician_id,
            'quotation_id' => $quotation_id,
            'scheduled_date' => $scheduled_date,
            'status' => sanitize_text_field($params['status'] ?? 'Scheduled'),
            'priority' => sanitize_text_field($params['priority'] ?? 'Medium'),
            'description' => sanitize_textarea_field($params['description'] ?? ''),
            'work_notes' => sanitize_textarea_field($params['work_notes'] ?? '')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s'];
        $inserted_id = $this->jobRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create job.');
        }

        AuthService::logActivity(
            get_current_user_id(),
            'JOB_CREATE',
            "Scheduled job $job_number for {$data['customer_name']} (Tech ID: $technician_id)"
        );

        return $this->success('Job scheduled successfully.', ['id' => $inserted_id, 'job_number' => $job_number], 201);
    }

    /**
     * PUT /jobs/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $job = $this->jobRepository->findById($id);

        if (!$job) {
            return $this->error('Job not found.', [], 404);
        }

        // Technicians can only update their own assigned jobs
        $is_technician_only = current_user_can('update_assigned_jobs') && !current_user_can('manage_jobs');
        if ($is_technician_only) {
            if (intval($job['technician_id']) !== get_current_user_id()) {
                return $this->error('Access Forbidden: You are not assigned to this job.', [], 403);
            }
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        // Common update fields
        if (isset($params['status'])) {
            $status = sanitize_text_field($params['status']);
            if (!in_array($status, ['Scheduled', 'In Progress', 'Completed', 'Cancelled'])) {
                return $this->error('Invalid job status.');
            }
            $data['status'] = $status;
            $formats[] = '%s';
        }

        if (isset($params['work_notes'])) {
            $data['work_notes'] = sanitize_textarea_field($params['work_notes']);
            $formats[] = '%s';
        }

        // Management-only update fields
        if (!$is_technician_only) {
            $fields = [
                'customer_name' => '%s',
                'phone' => '%s',
                'address' => '%s',
                'priority' => '%s',
                'description' => '%s',
                'scheduled_date' => '%s'
            ];

            foreach ($fields as $field => $format) {
                if (isset($params[$field])) {
                    if ($field === 'address' || $field === 'description') {
                        $data[$field] = sanitize_textarea_field($params[$field]);
                    } else {
                        $data[$field] = sanitize_text_field($params[$field]);
                    }
                    $formats[] = $format;
                }
            }

            if (isset($params['technician_id'])) {
                $tech_id = intval($params['technician_id']);
                if ($tech_id > 0) {
                    $tech = get_userdata($tech_id);
                    if (!$tech) {
                        return $this->error('Technician not found.');
                    }
                }
                $data['technician_id'] = $tech_id ?: null;
                $formats[] = '%d';
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->jobRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update job details.');
        }

        AuthService::logActivity(
            get_current_user_id(),
            'JOB_UPDATE',
            "Updated job ID: $id (Status: " . ($data['status'] ?? $job['status']) . ")"
        );

        return $this->success('Job updated successfully.', $this->jobRepository->findById($id));
    }

    /**
     * DELETE /jobs/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $job = $this->jobRepository->findById($id);

        if (!$job) {
            return $this->error('Job not found.', [], 404);
        }

        $deleted = $this->jobRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete job.');
        }

        AuthService::logActivity(get_current_user_id(), 'JOB_DELETE', "Soft deleted job ID: $id ({$job['job_number']})");

        return $this->success('Job deleted successfully.');
    }
}
