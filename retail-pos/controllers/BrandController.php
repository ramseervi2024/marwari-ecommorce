<?php
namespace RetailPosApi\Controllers;

use RetailPosApi\Repositories\BrandRepository;
use WP_REST_Request;

class BrandController extends BaseController {
    private $brandRepository;

    public function __construct() {
        $this->brandRepository = new BrandRepository();
    }

    /**
     * GET /brands
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'name', 'slug', 'status'];
        $search_fields = ['name', 'slug'];
        
        $results = $this->brandRepository->findAll($params, $allowed_sorts, $search_fields);
        return $this->success('Brands retrieved successfully.', $results);
    }

    /**
     * GET /brands/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $brand = $this->brandRepository->findById($id);
        
        if (!$brand) {
            return $this->error('Brand not found.', [], 404);
        }
        
        return $this->success('Brand retrieved successfully.', $brand);
    }

    /**
     * POST /brands
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
        
        $inserted_id = $this->brandRepository->create($data, ['%s', '%s', '%s']);
        if (!$inserted_id) {
            return $this->error('Failed to create brand.');
        }
        
        return $this->success('Brand created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /brands/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $brand = $this->brandRepository->findById($id);
        
        if (!$brand) {
            return $this->error('Brand not found.', [], 404);
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
        
        $updated = $this->brandRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update brand.');
        }
        
        return $this->success('Brand updated successfully.', $this->brandRepository->findById($id));
    }

    /**
     * DELETE /brands/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $brand = $this->brandRepository->findById($id);
        
        if (!$brand) {
            return $this->error('Brand not found.', [], 404);
        }
        
        if (!$this->brandRepository->delete($id)) {
            return $this->error('Failed to delete brand.');
        }
        
        return $this->success('Brand deleted successfully.');
    }
}
