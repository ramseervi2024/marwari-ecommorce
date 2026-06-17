<?php
namespace ConstructionManagementApi\Controllers;

use ConstructionManagementApi\Repositories\ProgressRepository;
use ConstructionManagementApi\Services\AuthService;
use WP_REST_Request;

class ProgressController extends BaseController {
    private $progressRepository;

    public function __construct() {
        $this->progressRepository = new ProgressRepository();
    }

    /**
     * GET /progress
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'project_id', 'work_category', 'planned_percentage', 'actual_percentage', 'update_date'];
        $search_fields = ['work_category', 'remarks'];

        $extra_filters = [];
        if (isset($params['project_id'])) {
            $extra_filters['project_id'] = intval($params['project_id']);
        }
        if (isset($params['work_category'])) {
            $extra_filters['work_category'] = sanitize_text_field($params['work_category']);
        }

        $results = $this->progressRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Progress tracking logs retrieved successfully.', $results);
    }

    /**
     * GET /progress/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $progress = $this->progressRepository->findById($id);

        if (!$progress) {
            return $this->error('Progress tracking record not found.', [], 404);
        }

        return $this->success('Progress tracking record retrieved successfully.', $progress);
    }

    /**
     * POST /progress
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['project_id']) || empty($params['work_category'])) {
            return $this->error('Validation failed: project_id and work_category are required.');
        }

        $data = [
            'project_id' => intval($params['project_id']),
            'work_category' => sanitize_text_field($params['work_category']),
            'planned_percentage' => isset($params['planned_percentage']) ? floatval($params['planned_percentage']) : 0.00,
            'actual_percentage' => isset($params['actual_percentage']) ? floatval($params['actual_percentage']) : 0.00,
            'remarks' => sanitize_textarea_field($params['remarks'] ?? ''),
            'photos' => sanitize_text_field($params['photos'] ?? ''),
            'update_date' => !empty($params['update_date']) ? sanitize_text_field($params['update_date']) : current_time('Y-m-d')
        ];

        $formats = ['%d', '%s', '%f', '%f', '%s', '%s', '%s'];
        $inserted_id = $this->progressRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create progress tracking record.');
        }

        AuthService::logActivity(get_current_user_id(), 'PROGRESS_CREATE', "Created progress tracking record ID: $inserted_id category: $data[work_category] actual: $data[actual_percentage]%");

        return $this->success('Progress tracking record created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /progress/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $progress = $this->progressRepository->findById($id);

        if (!$progress) {
            return $this->error('Progress tracking record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['project_id', 'work_category', 'planned_percentage', 'actual_percentage', 'remarks', 'photos', 'update_date'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'project_id') {
                    $data[$field] = intval($params[$field]);
                    $formats[] = '%d';
                } elseif ($field === 'planned_percentage' || $field === 'actual_percentage') {
                    $data[$field] = floatval($params[$field]);
                    $formats[] = '%f';
                } elseif ($field === 'remarks') {
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

        $updated = $this->progressRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update progress tracking record.');
        }

        AuthService::logActivity(get_current_user_id(), 'PROGRESS_UPDATE', "Updated progress tracking record ID: $id");

        return $this->success('Progress tracking record updated successfully.', $this->progressRepository->findById($id));
    }

    /**
     * DELETE /progress/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $progress = $this->progressRepository->findById($id);

        if (!$progress) {
            return $this->error('Progress tracking record not found.', [], 404);
        }

        $deleted = $this->progressRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete progress tracking record.');
        }

        AuthService::logActivity(get_current_user_id(), 'PROGRESS_DELETE', "Soft deleted progress log ID: $id");

        return $this->success('Progress tracking record deleted successfully.');
    }
}
