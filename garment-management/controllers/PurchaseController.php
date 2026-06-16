<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\PurchaseRepository;
use WP_REST_Request;

class PurchaseController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new PurchaseRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Purchase items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Purchase item not found.', [], 404);
        }
        return $this->success('Purchase item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['po_number']) || empty($params['supplier_id']) || empty($params['item_type']) || empty($params['item_id']) || empty($params['quantity']) || empty($params['rate'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['po_number'] = sanitize_text_field($params['po_number'] ?? '');
        $params['supplier_id'] = intval($params['supplier_id'] ?? 0);
        $params['item_type'] = sanitize_text_field($params['item_type'] ?? '');
        $params['item_id'] = intval($params['item_id'] ?? 0);
        $params['quantity'] = floatval($params['quantity'] ?? 0);
        $params['rate'] = floatval($params['rate'] ?? 0);
        $params['total_amount'] = floatval($params['total_amount'] ?? 0);
        $params['purchase_date'] = sanitize_text_field($params['purchase_date'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Purchase item.');
        }

        return $this->success('Purchase item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Purchase item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['po_number'])) $updates['po_number'] = sanitize_text_field($params['po_number']);
        if (isset($params['supplier_id'])) $updates['supplier_id'] = intval($params['supplier_id']);
        if (isset($params['item_type'])) $updates['item_type'] = sanitize_text_field($params['item_type']);
        if (isset($params['item_id'])) $updates['item_id'] = intval($params['item_id']);
        if (isset($params['quantity'])) $updates['quantity'] = floatval($params['quantity']);
        if (isset($params['rate'])) $updates['rate'] = floatval($params['rate']);
        if (isset($params['total_amount'])) $updates['total_amount'] = floatval($params['total_amount']);
        if (isset($params['purchase_date'])) $updates['purchase_date'] = sanitize_text_field($params['purchase_date']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Purchase item.');
        }

        return $this->success('Purchase item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Purchase item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Purchase item.');
        }
        return $this->success('Purchase item deleted successfully.');
    }
}
