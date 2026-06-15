<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\ParentRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class ParentController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new ParentRepository();
    }

    /**
     * GET /parents
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'father_name', 'mother_name'];
        $search_fields = ['father_name', 'mother_name', 'email', 'mobile', 'occupation'];
        
        $result = $this->repository->findAll($params, $allowed_sorts, $search_fields);
        return $this->success('Parents fetched successfully', $result);
    }

    /**
     * GET /parents/{id}
     */
    public function show(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $parent = $this->repository->findById($id);

        if (!$parent) {
            return $this->error('Parent not found.', [], 404);
        }

        return $this->success('Parent details fetched successfully', $parent);
    }

    /**
     * POST /parents
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['father_name']) || empty($params['mobile'])) {
            return $this->error('Validation failed: father_name and mobile are required.');
        }

        $data = [
            'father_name' => sanitize_text_field($params['father_name']),
            'mother_name' => isset($params['mother_name']) ? sanitize_text_field($params['mother_name']) : null,
            'mobile' => sanitize_text_field($params['mobile']),
            'email' => isset($params['email']) ? sanitize_email($params['email']) : null,
            'occupation' => isset($params['occupation']) ? sanitize_text_field($params['occupation']) : null,
            'address' => isset($params['address']) ? sanitize_textarea_field($params['address']) : null,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        $parent_id = $this->repository->create($data, $formats);

        if (!$parent_id) {
            return $this->error('Failed to create parent profile.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_PARENT', "Registered parent profile ID: $parent_id");

        return $this->success('Parent profile created successfully', array_merge(['id' => $parent_id], $data), 201);
    }

    /**
     * PUT /parents/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $parent = $this->repository->findById($id);

        if (!$parent) {
            return $this->error('Parent profile not found.', [], 404);
        }

        $params = $request->get_json_params();
        $update_data = [];
        $formats = [];

        $allowed_fields = [
            'father_name' => '%s',
            'mother_name' => '%s',
            'mobile' => '%s',
            'email' => '%s',
            'occupation' => '%s',
            'address' => '%s'
        ];

        foreach ($allowed_fields as $field => $format) {
            if (isset($params[$field])) {
                if ($field === 'email') {
                    $update_data[$field] = sanitize_email($params[$field]);
                } elseif ($field === 'address') {
                    $update_data[$field] = sanitize_textarea_field($params[$field]);
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
            return $this->error('Failed to update parent profile.');
        }

        AuthService::logActivity(get_current_user_id(), 'UPDATE_PARENT', "Updated parent details ID: $id");

        return $this->success('Parent profile updated successfully', $this->repository->findById($id));
    }

    /**
     * DELETE /parents/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $parent = $this->repository->findById($id);

        if (!$parent) {
            return $this->error('Parent profile not found.', [], 404);
        }

        $success = $this->repository->delete($id);

        if (!$success) {
            return $this->error('Failed to delete parent profile.');
        }

        AuthService::logActivity(get_current_user_id(), 'DELETE_PARENT', "Soft deleted parent ID: $id");

        return $this->success('Parent profile soft deleted successfully');
    }
}
