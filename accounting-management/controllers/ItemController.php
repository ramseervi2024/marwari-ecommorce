<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\ItemRepository;
use AccountingManagementApi\Services\AuthService;
use WP_REST_Request;

class ItemController extends BaseController {
    private $itemRepository;

    public function __construct() {
        $this->itemRepository = new ItemRepository();
    }

    /**
     * GET /items
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'item_code', 'item_name', 'created_at', 'selling_price', 'stock_quantity'];
        $search_fields = ['item_code', 'item_name', 'hsn_sac_code', 'item_type'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['item_type'])) {
            $extra_filters['item_type'] = sanitize_text_field($params['item_type']);
        }

        $results = $this->itemRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Items retrieved successfully.', $results);
    }

    /**
     * GET /items/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->itemRepository->findById($id);

        if (!$item) {
            return $this->error('Item not found.', [], 404);
        }

        return $this->success('Item retrieved successfully.', $item);
    }

    /**
     * POST /items
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['item_name'])) {
            return $this->error('Validation failed: item_name is required.');
        }

        // Generate item code
        $item_code = 'ITEM-ACC-' . sprintf('%04d', rand(1000, 9999));
        while ($this->itemRepository->existsItemCode($item_code)) {
            $item_code = 'ITEM-ACC-' . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'item_code' => $item_code,
            'item_name' => sanitize_text_field($params['item_name']),
            'item_type' => sanitize_text_field($params['item_type'] ?? 'Product'),
            'hsn_sac_code' => sanitize_text_field($params['hsn_sac_code'] ?? ''),
            'unit' => sanitize_text_field($params['unit'] ?? 'PCS'),
            'purchase_price' => floatval($params['purchase_price'] ?? 0.00),
            'selling_price' => floatval($params['selling_price'] ?? 0.00),
            'gst_percentage' => floatval($params['gst_percentage'] ?? 18.00),
            'stock_quantity' => intval($params['stock_quantity'] ?? 0),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%f', '%f', '%f', '%d', '%s'];
        $inserted_id = $this->itemRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create item.');
        }

        AuthService::logActivity(get_current_user_id(), 'ITEM_CREATE', "Created item $item_code ($inserted_id)");

        return $this->success('Item created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /items/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->itemRepository->findById($id);

        if (!$item) {
            return $this->error('Item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = [
            'item_name' => '%s',
            'item_type' => '%s',
            'hsn_sac_code' => '%s',
            'unit' => '%s',
            'purchase_price' => '%f',
            'selling_price' => '%f',
            'gst_percentage' => '%f',
            'stock_quantity' => '%d',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%f') {
                    $data[$field] = floatval($params[$field]);
                } elseif ($format === '%d') {
                    $data[$field] = intval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->itemRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update item details.');
        }

        AuthService::logActivity(get_current_user_id(), 'ITEM_UPDATE', "Updated item ID: $id");

        return $this->success('Item updated successfully.', $this->itemRepository->findById($id));
    }

    /**
     * DELETE /items/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->itemRepository->findById($id);

        if (!$item) {
            return $this->error('Item not found.', [], 404);
        }

        $deleted = $this->itemRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete item.');
        }

        AuthService::logActivity(get_current_user_id(), 'ITEM_DELETE', "Soft deleted item ID: $id ($item[item_code])");

        return $this->success('Item deleted successfully.');
    }
}
