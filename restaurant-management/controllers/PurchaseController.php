<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\PurchaseRepository;
use WP_REST_Request;

class PurchaseController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new PurchaseRepository();
    }

    public function getPurchases(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $purchases = $this->repository->all($limit, $offset);
        foreach ($purchases as &$purchase) {
            $purchase['purchase_items'] = $this->repository->getItems($purchase['id']);
        }
        return $this->success('Purchases retrieved successfully.', $purchases);
    }

    public function createPurchase(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['supplier_id']) || empty($params['purchase_items'])) {
            return $this->error('Validation failed: supplier_id and purchase_items array are required.');
        }

        $total_amount = 0.00;
        foreach ($params['purchase_items'] as $item) {
            $total_amount += floatval($item['price']) * floatval($item['quantity']);
        }

        $data = [
            'supplier_id' => intval($params['supplier_id']),
            'total_amount' => $total_amount,
            'status' => sanitize_text_field($params['status'] ?? 'Pending')
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to create purchase record.');
        }

        $this->repository->saveItems($id, $params['purchase_items']);

        $data['id'] = $id;
        $data['purchase_items'] = $this->repository->getItems($id);

        return $this->success('Purchase logged successfully and ingredient stock replenished.', $data, 201);
    }

    public function deletePurchase(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $purchase = $this->repository->find($id);
        if (!$purchase) {
            return $this->error('Purchase record not found.', [], 404);
        }

        if (!$this->repository->delete($id)) {
            return $this->error('Failed to delete purchase record.');
        }

        return $this->success('Purchase record deleted successfully.');
    }
}
