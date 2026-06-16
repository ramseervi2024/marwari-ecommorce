<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\BranchRepository;
use WP_REST_Request;

class BranchController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new BranchRepository();
    }

    public function getBranches(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $branches = $this->repository->all($limit, $offset);
        return $this->success('Branches retrieved successfully.', $branches);
    }

    public function createBranch(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['branch_name']) || empty($params['branch_code'])) {
            return $this->error('Validation failed: branch_name and branch_code are required.');
        }

        $data = [
            'branch_name' => sanitize_text_field($params['branch_name']),
            'branch_code' => sanitize_text_field($params['branch_code']),
            'address' => sanitize_text_field($params['address'] ?? ''),
            'manager' => sanitize_text_field($params['manager'] ?? ''),
            'status' => sanitize_text_field($params['status'] ?? 'Active')
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to register branch.');
        }

        $data['id'] = $id;
        return $this->success('Branch registered successfully.', $data, 201);
    }

    public function updateBranch(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $params = $request->get_json_params();

        $branch = $this->repository->find($id);
        if (!$branch) {
            return $this->error('Branch not found.', [], 404);
        }

        $data = [];
        if (isset($params['branch_name'])) $data['branch_name'] = sanitize_text_field($params['branch_name']);
        if (isset($params['branch_code'])) $data['branch_code'] = sanitize_text_field($params['branch_code']);
        if (isset($params['address'])) $data['address'] = sanitize_text_field($params['address']);
        if (isset($params['manager'])) $data['manager'] = sanitize_text_field($params['manager']);
        if (isset($params['status'])) $data['status'] = sanitize_text_field($params['status']);

        if (empty($data)) {
            return $this->error('No fields provided to update.');
        }

        if (!$this->repository->update($id, $data)) {
            return $this->error('Failed to update branch.');
        }

        return $this->success('Branch updated successfully.', array_merge($branch, $data));
    }

    public function deleteBranch(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $branch = $this->repository->find($id);
        if (!$branch) {
            return $this->error('Branch not found.', [], 404);
        }

        if (!$this->repository->delete($id)) {
            return $this->error('Failed to delete branch.');
        }

        return $this->success('Branch deleted successfully.');
    }
}
