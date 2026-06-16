<?php
namespace RetailPosApi\Controllers;

use RetailPosApi\Repositories\CategoryRepository;
use WP_REST_Request;

class CategoryController extends BaseController {
    private $categoryRepository;

    public function __construct() {
        $this->categoryRepository = new CategoryRepository();
    }

    /**
     * GET /categories
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'name', 'slug', 'status'];
        $search_fields = ['name', 'slug'];
        
        $results = $this->categoryRepository->findAll($params, $allowed_sorts, $search_fields);
        return $this->success('Categories retrieved successfully.', $results);
    }

    /**
     * GET /categories/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $cat = $this->categoryRepository->findById($id);
        
        if (!$cat) {
            return $this->error('Category not found.', [], 404);
        }
        
        return $this->success('Category retrieved successfully.', $cat);
    }

    /**
     * POST /categories
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        
        if (empty($params['name'])) {
            return $this->error('Validation failed: name is required.');
        }
        
        $slug = !empty($params['slug']) ? sanitize_title($params['slug']) : sanitize_title($params['name']);
        
        $data = [
            'name' => sanitize_text_field($params['name']),
            'slug' => $slug,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];
        
        $inserted_id = $this->categoryRepository->create($data, ['%s', '%s', '%s']);
        if (!$inserted_id) {
            return $this->error('Failed to create category.');
        }
        
        return $this->success('Category created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /categories/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $cat = $this->categoryRepository->findById($id);
        
        if (!$cat) {
            return $this->error('Category not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        if (isset($params['name'])) {
            $data['name'] = sanitize_text_field($params['name']);
            $formats[] = '%s';
        }
        if (isset($params['slug'])) {
            $data['slug'] = sanitize_title($params['slug']);
            $formats[] = '%s';
        }
        if (isset($params['status'])) {
            $data['status'] = sanitize_text_field($params['status']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }
        
        $updated = $this->categoryRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update category.');
        }
        
        return $this->success('Category updated successfully.', $this->categoryRepository->findById($id));
    }

    /**
     * DELETE /categories/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $cat = $this->categoryRepository->findById($id);
        
        if (!$cat) {
            return $this->error('Category not found.', [], 404);
        }
        
        if (!$this->categoryRepository->delete($id)) {
            return $this->error('Failed to delete category.');
        }
        
        return $this->success('Category deleted successfully.');
    }
}
