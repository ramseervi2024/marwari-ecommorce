<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\CategoryRepository;
use WP_REST_Request;

class CategoryController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new CategoryRepository();
    }

    public function getCategories(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $categories = $this->repository->all($limit, $offset);
        return $this->success('Categories retrieved successfully.', $categories);
    }

    public function createCategory(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['name'])) {
            return $this->error('Validation failed: name is required.');
        }

        $data = [
            'name' => sanitize_text_field($params['name'])
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to create category.');
        }

        $data['id'] = $id;
        return $this->success('Category created successfully.', $data, 201);
    }

    public function updateCategory(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $params = $request->get_json_params();

        $category = $this->repository->find($id);
        if (!$category) {
            return $this->error('Category not found.', [], 404);
        }

        $data = [];
        if (isset($params['name'])) $data['name'] = sanitize_text_field($params['name']);

        if (empty($data)) {
            return $this->error('No fields provided to update.');
        }

        if (!$this->repository->update($id, $data)) {
            return $this->error('Failed to update category.');
        }

        return $this->success('Category updated successfully.', array_merge($category, $data));
    }

    public function deleteCategory(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $category = $this->repository->find($id);
        if (!$category) {
            return $this->error('Category not found.', [], 404);
        }

        if (!$this->repository->delete($id)) {
            return $this->error('Failed to delete category.');
        }

        return $this->success('Category deleted successfully.');
    }
}
