<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\SupplierRepository;
use WP_REST_Request;

class SupplierController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new SupplierRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Supplier items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Supplier item not found.', [], 404);
        }
        return $this->success('Supplier item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['supplier_name'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['supplier_name'] = sanitize_text_field($params['supplier_name'] ?? '');
        $params['mobile'] = sanitize_text_field($params['mobile'] ?? '');
        $params['email'] = sanitize_email($params['email'] ?? '');
        $params['gst_number'] = sanitize_text_field($params['gst_number'] ?? '');
        $params['address'] = sanitize_textarea_field($params['address'] ?? '');
        $params['rating'] = floatval($params['rating'] ?? 0);
        $params['status'] = sanitize_text_field($params['status'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Supplier item.');
        }

        return $this->success('Supplier item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Supplier item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['supplier_name'])) $updates['supplier_name'] = sanitize_text_field($params['supplier_name']);
        if (isset($params['mobile'])) $updates['mobile'] = sanitize_text_field($params['mobile']);
        if (isset($params['email'])) $updates['email'] = sanitize_email($params['email']);
        if (isset($params['gst_number'])) $updates['gst_number'] = sanitize_text_field($params['gst_number']);
        if (isset($params['address'])) $updates['address'] = sanitize_textarea_field($params['address']);
        if (isset($params['rating'])) $updates['rating'] = floatval($params['rating']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Supplier item.');
        }

        return $this->success('Supplier item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Supplier item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Supplier item.');
        }
        return $this->success('Supplier item deleted successfully.');
    }
}
