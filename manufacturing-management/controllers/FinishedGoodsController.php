<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\FinishedGoodsRepository;
use WP_REST_Request;

class FinishedGoodsController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new FinishedGoodsRepository();
    }

    public function index(WP_REST_Request $request) {
        $items = $this->repo->all();
        return $this->success('Finished goods retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Finished product not found.', [], 404);
        }
        return $this->success('Finished product retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['product_code']) || empty($params['product_name'])) {
            return $this->error('Validation failed: product_code and product_name are required.');
        }

        $params['quantity'] = floatval($params['quantity'] ?? 0);
        $params['selling_price'] = floatval($params['selling_price'] ?? 0);
        $params['warehouse'] = sanitize_text_field($params['warehouse'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? 'ACTIVE');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create finished product. Ensure product_code is unique.');
        }

        return $this->success('Finished product created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Finished product not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['product_name'])) $updates['product_name'] = sanitize_text_field($params['product_name']);
        if (isset($params['quantity'])) $updates['quantity'] = floatval($params['quantity']);
        if (isset($params['selling_price'])) $updates['selling_price'] = floatval($params['selling_price']);
        if (isset($params['warehouse'])) $updates['warehouse'] = sanitize_text_field($params['warehouse']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update finished product.');
        }

        return $this->success('Finished product updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Finished product not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete finished product.');
        }
        return $this->success('Finished product deleted successfully.');
    }
}
