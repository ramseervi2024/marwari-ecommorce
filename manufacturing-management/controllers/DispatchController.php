<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\DispatchRepository;
use ManufacturingManagementApi\Repositories\FinishedGoodsRepository;
use ManufacturingManagementApi\Repositories\InventoryRepository;
use WP_REST_Request;

class DispatchController extends BaseController {
    private $repo;
    private $fgRepo;
    private $invRepo;

    public function __construct() {
        $this->repo = new DispatchRepository();
        $this->fgRepo = new FinishedGoodsRepository();
        $this->invRepo = new InventoryRepository();
    }

    public function index(WP_REST_Request $request) {
        $items = $this->repo->all();
        foreach ($items as &$item) {
            $product = $this->fgRepo->find(intval($item['product_id']));
            $item['product_name'] = $product ? $product['product_name'] : 'Unknown';
            $item['product_code'] = $product ? $product['product_code'] : 'Unknown';
        }
        return $this->success('Dispatches retrieved successfully.', $items);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['dispatch_number']) || empty($params['customer_id']) || empty($params['product_id']) || empty($params['quantity'])) {
            return $this->error('Validation failed: dispatch_number, customer_id, product_id, and quantity are required.');
        }

        $prod = $this->fgRepo->find(intval($params['product_id']));
        if (!$prod) {
            return $this->error('Finished product not found.');
        }

        $params['quantity'] = floatval($params['quantity']);
        if (floatval($prod['quantity']) < $params['quantity']) {
            return $this->error("Insufficient stock of finished product '{$prod['product_name']}'. Current stock: {$prod['quantity']}, Requested dispatch: {$params['quantity']}.");
        }

        $params['customer_id'] = intval($params['customer_id']);
        $params['vehicle_number'] = sanitize_text_field($params['vehicle_number'] ?? '');
        $params['driver_name'] = sanitize_text_field($params['driver_name'] ?? '');
        $params['dispatch_date'] = !empty($params['dispatch_date']) ? sanitize_text_field($params['dispatch_date']) : current_time('mysql');
        $params['delivery_date'] = !empty($params['delivery_date']) ? sanitize_text_field($params['delivery_date']) : null;
        $params['status'] = sanitize_text_field($params['status'] ?? 'PENDING');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create dispatch log. Ensure dispatch_number is unique.');
        }

        // Decrement finished goods stock
        $new_fg_stock = floatval($prod['quantity']) - $params['quantity'];
        $this->fgRepo->update($prod['id'], ['quantity' => $new_fg_stock]);
        $this->invRepo->logMovement('FINISHED', $prod['id'], 'OUT', $params['quantity'], 'Dispatch: ' . $params['dispatch_number']);

        return $this->success('Dispatch log recorded successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Dispatch log not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        if (isset($params['delivery_date'])) $updates['delivery_date'] = sanitize_text_field($params['delivery_date']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update dispatch log.');
        }

        return $this->success('Dispatch log updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Dispatch log not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete dispatch log.');
        }
        return $this->success('Dispatch log deleted successfully.');
    }
}
