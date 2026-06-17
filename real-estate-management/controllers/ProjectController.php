<?php
namespace RealEstateManagementApi\Controllers;

use RealEstateManagementApi\Repositories\ProjectRepository;
use RealEstateManagementApi\Services\AuthService;
use WP_REST_Request;

class ProjectController extends BaseController {
    private $projectRepository;

    public function __construct() {
        $this->projectRepository = new ProjectRepository();
    }

    /**
     * GET /projects
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'project_code', 'project_name', 'location', 'builder_name', 'status', 'created_at'];
        $search_fields = ['project_code', 'project_name', 'location', 'builder_name'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->projectRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Projects retrieved successfully.', $results);
    }

    /**
     * GET /projects/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $project = $this->projectRepository->findById($id);

        if (!$project) {
            return $this->error('Project not found.', [], 404);
        }

        return $this->success('Project retrieved successfully.', $project);
    }

    /**
     * POST /projects
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['project_name'])) {
            return $this->error('Validation failed: project_name is required.');
        }

        // Generate custom project code
        $project_code = 'PRJ-RE-' . sprintf('%04d', rand(1000, 9999));
        while ($this->projectRepository->existsProjectCode($project_code)) {
            $project_code = 'PRJ-RE-' . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'project_code' => $project_code,
            'project_name' => sanitize_text_field($params['project_name']),
            'location' => sanitize_text_field($params['location'] ?? ''),
            'builder_name' => sanitize_text_field($params['builder_name'] ?? ''),
            'launch_date' => !empty($params['launch_date']) ? sanitize_text_field($params['launch_date']) : null,
            'completion_date' => !empty($params['completion_date']) ? sanitize_text_field($params['completion_date']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'Planning')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        $inserted_id = $this->projectRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create project.');
        }

        AuthService::logActivity(get_current_user_id(), 'PROJECT_CREATE', "Created project code $project_code ($inserted_id)");

        return $this->success('Project created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /projects/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $project = $this->projectRepository->findById($id);

        if (!$project) {
            return $this->error('Project not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['project_name', 'location', 'builder_name', 'launch_date', 'completion_date', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                $data[$field] = sanitize_text_field($params[$field]);
                $formats[] = '%s';
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->projectRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update project.');
        }

        AuthService::logActivity(get_current_user_id(), 'PROJECT_UPDATE', "Updated project ID: $id");

        return $this->success('Project updated successfully.', $this->projectRepository->findById($id));
    }

    /**
     * DELETE /projects/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $project = $this->projectRepository->findById($id);

        if (!$project) {
            return $this->error('Project not found.', [], 404);
        }

        $deleted = $this->projectRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete project.');
        }

        AuthService::logActivity(get_current_user_id(), 'PROJECT_DELETE', "Soft deleted project ID: $id ($project[project_code])");

        return $this->success('Project deleted successfully.');
    }
}
