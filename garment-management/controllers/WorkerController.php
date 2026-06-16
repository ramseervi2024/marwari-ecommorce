<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\WorkerRepository;
use WP_REST_Request;

class WorkerController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new WorkerRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Worker items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Worker item not found.', [], 404);
        }
        return $this->success('Worker item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['employee_code']) || empty($params['name'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['employee_code'] = sanitize_text_field($params['employee_code'] ?? '');
        $params['name'] = sanitize_text_field($params['name'] ?? '');
        $params['mobile'] = sanitize_text_field($params['mobile'] ?? '');
        $params['department'] = sanitize_text_field($params['department'] ?? '');
        $params['designation'] = sanitize_text_field($params['designation'] ?? '');
        $params['salary_type'] = sanitize_text_field($params['salary_type'] ?? '');
        $params['daily_wage'] = floatval($params['daily_wage'] ?? 0);
        $params['monthly_salary'] = floatval($params['monthly_salary'] ?? 0);
        $params['attendance_status'] = sanitize_text_field($params['attendance_status'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Worker item.');
        }

        return $this->success('Worker item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Worker item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['employee_code'])) $updates['employee_code'] = sanitize_text_field($params['employee_code']);
        if (isset($params['name'])) $updates['name'] = sanitize_text_field($params['name']);
        if (isset($params['mobile'])) $updates['mobile'] = sanitize_text_field($params['mobile']);
        if (isset($params['department'])) $updates['department'] = sanitize_text_field($params['department']);
        if (isset($params['designation'])) $updates['designation'] = sanitize_text_field($params['designation']);
        if (isset($params['salary_type'])) $updates['salary_type'] = sanitize_text_field($params['salary_type']);
        if (isset($params['daily_wage'])) $updates['daily_wage'] = floatval($params['daily_wage']);
        if (isset($params['monthly_salary'])) $updates['monthly_salary'] = floatval($params['monthly_salary']);
        if (isset($params['attendance_status'])) $updates['attendance_status'] = sanitize_text_field($params['attendance_status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Worker item.');
        }

        return $this->success('Worker item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Worker item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Worker item.');
        }
        return $this->success('Worker item deleted successfully.');
    }
}
