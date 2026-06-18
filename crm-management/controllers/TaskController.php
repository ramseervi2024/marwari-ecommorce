<?php
namespace CrmManagementApi\Controllers;

use CrmManagementApi\Repositories\TaskRepository;
use CrmManagementApi\Repositories\LeadRepository;
use CrmManagementApi\Services\AuthService;
use WP_REST_Request;

class TaskController extends BaseController {
    private $taskRepository;
    private $leadRepository;

    public function __construct() {
        $this->taskRepository = new TaskRepository();
        $this->leadRepository = new LeadRepository();
    }

    /**
     * GET /tasks
     */
    public function getTasks(WP_REST_Request $request) {
        $params = $request->get_params();
        $current_user = wp_get_current_user();

        $allowed_sorts = ['id', 'title', 'due_date', 'status', 'priority'];
        $extra_filters = [];

        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['priority'])) {
            $extra_filters['priority'] = sanitize_text_field($params['priority']);
        }
        if (isset($params['lead_id'])) {
            $extra_filters['lead_id'] = intval($params['lead_id']);
        }

        // Executive / Telecaller restriction: see only their assigned tasks
        if (!current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            $extra_filters['assigned_to'] = $current_user->ID;
        } elseif (isset($params['assigned_to'])) {
            $extra_filters['assigned_to'] = intval($params['assigned_to']);
        }

        $results = $this->taskRepository->findAll($params, $allowed_sorts, ['title', 'description'], $extra_filters);

        // Map names
        foreach ($results['data'] as &$row) {
            if ($row['assigned_to']) {
                $assigned = get_userdata($row['assigned_to']);
                $row['assigned_name'] = $assigned ? $assigned->display_name : 'Unknown';
            } else {
                $row['assigned_name'] = 'Unassigned';
            }

            if ($row['lead_id']) {
                $lead = $this->leadRepository->findById($row['lead_id']);
                $row['lead_name'] = $lead ? $lead['first_name'] . ' ' . $lead['last_name'] : '';
            } else {
                $row['lead_name'] = '';
            }
        }

        return $this->success('Tasks list retrieved.', $results);
    }

    /**
     * GET /tasks/{id}
     */
    public function getTask(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $task = $this->taskRepository->findById($id);
        if (!$task) {
            return $this->error('Task not found.', [], 404);
        }
        if ($task['assigned_to']) {
            $assigned = get_userdata($task['assigned_to']);
            $task['assigned_name'] = $assigned ? $assigned->display_name : 'Unknown';
        }
        return $this->success('Task retrieved.', $task);
    }

    /**
     * POST /tasks
     */
    public function createTask(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['title']) || empty($params['due_date'])) {
            return $this->error('title and due_date are required.');
        }

        $assigned_to = isset($params['assigned_to']) ? intval($params['assigned_to']) : get_current_user_id();

        $data = [
            'title'       => sanitize_text_field($params['title']),
            'description' => sanitize_textarea_field($params['description'] ?? ''),
            'due_date'    => sanitize_text_field($params['due_date']),
            'status'      => sanitize_text_field($params['status'] ?? 'Pending'),
            'priority'    => sanitize_text_field($params['priority'] ?? 'Medium'),
            'assigned_to' => $assigned_to,
            'lead_id'     => !empty($params['lead_id']) ? intval($params['lead_id']) : null
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%d', '%d'];

        $id = $this->taskRepository->create($data, $formats);
        if (!$id) {
            return $this->error('Failed to create task.');
        }

        AuthService::logActivity(get_current_user_id(), 'TASK_CREATE', "Created task: $data[title]");

        return $this->success('Task created successfully.', $this->taskRepository->findById($id), 201);
    }

    /**
     * PUT /tasks/{id}
     */
    public function updateTask(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $task = $this->taskRepository->findById($id);

        if (!$task) {
            return $this->error('Task not found.', [], 404);
        }

        // Privilege check
        $current_user = wp_get_current_user();
        if (!current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            if (intval($task['assigned_to']) !== $current_user->ID) {
                return $this->error('Access Denied.', [], 403);
            }
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'title'       => '%s',
            'description' => '%s',
            'due_date'    => '%s',
            'status'      => '%s',
            'priority'    => '%s',
            'assigned_to' => '%d',
            'lead_id'     => '%d'
        ];

        foreach ($fields as $key => $fmt) {
            if (isset($params[$key])) {
                if ($key === 'description') {
                    $data[$key] = sanitize_textarea_field($params[$key]);
                } elseif ($key === 'assigned_to' || $key === 'lead_id') {
                    $data[$key] = intval($params[$key]);
                } else {
                    $data[$key] = sanitize_text_field($params[$key]);
                }
                $formats[] = $fmt;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->taskRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update task.');
        }

        AuthService::logActivity(get_current_user_id(), 'TASK_UPDATE', "Updated task ID: $id ($task[title])");

        return $this->success('Task updated successfully.', $this->taskRepository->findById($id));
    }

    /**
     * DELETE /tasks/{id}
     */
    public function deleteTask(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $task = $this->taskRepository->findById($id);

        if (!$task) {
            return $this->error('Task not found.', [], 404);
        }

        // Privilege check
        $current_user = wp_get_current_user();
        if (!current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            if (intval($task['assigned_to']) !== $current_user->ID) {
                return $this->error('Access Denied.', [], 403);
            }
        }

        $deleted = $this->taskRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete task.');
        }

        AuthService::logActivity(get_current_user_id(), 'TASK_DELETE', "Deleted task ID: $id ($task[title])");

        return $this->success('Task deleted successfully.');
    }
}
