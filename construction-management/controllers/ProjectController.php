<?php
namespace ConstructionManagementApi\Controllers;

use ConstructionManagementApi\Repositories\ProjectRepository;
use ConstructionManagementApi\Repositories\MilestoneRepository;
use ConstructionManagementApi\Services\AuthService;
use WP_REST_Request;

class ProjectController extends BaseController {
    private $projectRepository;
    private $milestoneRepository;

    public function __construct() {
        $this->projectRepository = new ProjectRepository();
        $this->milestoneRepository = new MilestoneRepository();
    }

    /**
     * GET /projects
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'project_code', 'project_name', 'status', 'created_at', 'estimated_cost', 'actual_cost'];
        $search_fields = ['project_code', 'project_name', 'client_name', 'project_type', 'location', 'project_manager'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['project_type'])) {
            $extra_filters['project_type'] = sanitize_text_field($params['project_type']);
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
        $project_code = 'PRJ-' . date('Y') . '-' . sprintf('%03d', rand(100, 999));
        while ($this->projectRepository->existsProjectCode($project_code)) {
            $project_code = 'PRJ-' . date('Y') . '-' . sprintf('%03d', rand(100, 999));
        }

        $data = [
            'project_code' => $project_code,
            'project_name' => sanitize_text_field($params['project_name']),
            'client_name' => sanitize_text_field($params['client_name'] ?? ''),
            'project_type' => sanitize_text_field($params['project_type'] ?? ''),
            'location' => sanitize_text_field($params['location'] ?? ''),
            'start_date' => !empty($params['start_date']) ? sanitize_text_field($params['start_date']) : null,
            'end_date' => !empty($params['end_date']) ? sanitize_text_field($params['end_date']) : null,
            'estimated_cost' => isset($params['estimated_cost']) ? floatval($params['estimated_cost']) : 0.00,
            'actual_cost' => isset($params['actual_cost']) ? floatval($params['actual_cost']) : 0.00,
            'project_manager' => sanitize_text_field($params['project_manager'] ?? ''),
            'status' => sanitize_text_field($params['status'] ?? 'Planning')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s'];
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
        
        $fields = ['project_name', 'client_name', 'project_type', 'location', 'start_date', 'end_date', 'estimated_cost', 'actual_cost', 'project_manager', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'estimated_cost' || $field === 'actual_cost') {
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

    // --- MILESTONES ACTIONS ---

    /**
     * GET /milestones
     */
    public function getAllMilestones(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'project_id', 'milestone_name', 'planned_date', 'actual_date', 'completion_percentage', 'status'];
        
        $extra_filters = [];
        if (isset($params['project_id'])) {
            $extra_filters['project_id'] = intval($params['project_id']);
        }
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->milestoneRepository->findAll($params, $allowed_sorts, ['milestone_name', 'status'], $extra_filters);
        return $this->success('Milestones retrieved successfully.', $results);
    }

    /**
     * POST /milestones
     */
    public function createMilestone(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['project_id']) || empty($params['milestone_name'])) {
            return $this->error('Validation failed: project_id and milestone_name are required.');
        }

        $data = [
            'project_id' => intval($params['project_id']),
            'milestone_name' => sanitize_text_field($params['milestone_name']),
            'planned_date' => !empty($params['planned_date']) ? sanitize_text_field($params['planned_date']) : null,
            'actual_date' => !empty($params['actual_date']) ? sanitize_text_field($params['actual_date']) : null,
            'completion_percentage' => isset($params['completion_percentage']) ? floatval($params['completion_percentage']) : 0.00,
            'status' => sanitize_text_field($params['status'] ?? 'Pending')
        ];

        $formats = ['%d', '%s', '%s', '%s', '%f', '%s'];
        $inserted_id = $this->milestoneRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create milestone.');
        }

        AuthService::logActivity(get_current_user_id(), 'MILESTONE_CREATE', "Created milestone ID: $inserted_id for project ID: $params[project_id]");

        return $this->success('Milestone created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /milestones/:id
     */
    public function updateMilestone(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $milestone = $this->milestoneRepository->findById($id);

        if (!$milestone) {
            return $this->error('Milestone not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['milestone_name', 'planned_date', 'actual_date', 'completion_percentage', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'completion_percentage') {
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

        $updated = $this->milestoneRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update milestone.');
        }

        AuthService::logActivity(get_current_user_id(), 'MILESTONE_UPDATE', "Updated milestone ID: $id");

        return $this->success('Milestone updated successfully.', $this->milestoneRepository->findById($id));
    }

    /**
     * DELETE /milestones/:id
     */
    public function deleteMilestone(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $milestone = $this->milestoneRepository->findById($id);

        if (!$milestone) {
            return $this->error('Milestone not found.', [], 404);
        }

        $deleted = $this->milestoneRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete milestone.');
        }

        AuthService::logActivity(get_current_user_id(), 'MILESTONE_DELETE', "Soft deleted milestone ID: $id");

        return $this->success('Milestone deleted successfully.');
    }
}
