<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\InventoryRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class InventoryController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new InventoryRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Finished items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Finished item not found.', [], 404);
        }
        return $this->success('Finished item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['product_name']) || empty($params['barcode'])) {
            return $this->error('Validation failed: missing required product_name or barcode.');
        }

        // Check if barcode already exists
        if ($this->repo->getByBarcode($params['barcode'])) {
            return $this->error('A product with this barcode already exists.');
        }

        $params['barcode'] = sanitize_text_field($params['barcode']);
        $params['sku'] = sanitize_text_field($params['sku'] ?? '');
        $params['product_name'] = sanitize_text_field($params['product_name']);
        $params['category'] = sanitize_text_field($params['category'] ?? '');
        $params['metal_type'] = sanitize_text_field($params['metal_type'] ?? 'Gold');
        $params['purity'] = sanitize_text_field($params['purity'] ?? '');
        $params['gross_weight'] = floatval($params['gross_weight'] ?? 0);
        $params['stone_weight'] = floatval($params['stone_weight'] ?? 0);
        $params['net_weight'] = floatval($params['net_weight'] ?? ($params['gross_weight'] - $params['stone_weight']));
        $params['making_charges'] = floatval($params['making_charges'] ?? 0);
        $params['purchase_price'] = floatval($params['purchase_price'] ?? 0);
        $params['selling_price'] = floatval($params['selling_price'] ?? 0);
        $params['hallmark_number'] = sanitize_text_field($params['hallmark_number'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? 'ACTIVE');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create finished item.');
        }

        AuthService::logActivity(get_current_user_id(), 'INVENTORY_CREATE', "Created finished ornament: {$params['product_name']} [{$params['barcode']}]");

        return $this->success('Finished item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Finished item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['barcode'])) $updates['barcode'] = sanitize_text_field($params['barcode']);
        if (isset($params['sku'])) $updates['sku'] = sanitize_text_field($params['sku']);
        if (isset($params['product_name'])) $updates['product_name'] = sanitize_text_field($params['product_name']);
        if (isset($params['category'])) $updates['category'] = sanitize_text_field($params['category']);
        if (isset($params['metal_type'])) $updates['metal_type'] = sanitize_text_field($params['metal_type']);
        if (isset($params['purity'])) $updates['purity'] = sanitize_text_field($params['purity']);
        if (isset($params['gross_weight'])) $updates['gross_weight'] = floatval($params['gross_weight']);
        if (isset($params['stone_weight'])) $updates['stone_weight'] = floatval($params['stone_weight']);
        if (isset($params['net_weight'])) $updates['net_weight'] = floatval($params['net_weight']);
        if (isset($params['making_charges'])) $updates['making_charges'] = floatval($params['making_charges']);
        if (isset($params['purchase_price'])) $updates['purchase_price'] = floatval($params['purchase_price']);
        if (isset($params['selling_price'])) $updates['selling_price'] = floatval($params['selling_price']);
        if (isset($params['hallmark_number'])) $updates['hallmark_number'] = sanitize_text_field($params['hallmark_number']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update finished item.');
        }

        AuthService::logActivity(get_current_user_id(), 'INVENTORY_UPDATE', "Updated finished ornament ID $id");

        return $this->success('Finished item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Finished item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete finished item.');
        }

        AuthService::logActivity(get_current_user_id(), 'INVENTORY_DELETE', "Deleted finished ornament: {$item['product_name']}");

        return $this->success('Finished item deleted successfully.');
    }
}
