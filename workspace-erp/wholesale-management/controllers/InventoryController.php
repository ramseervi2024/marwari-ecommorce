<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\InventoryRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class InventoryController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new InventoryRepository();
    }

    public function getInventory(WP_REST_Request $request) {
        $searchable = ['batch_number'];
        $sortable = ['id', 'product_id', 'warehouse_id', 'available_stock', 'minimum_stock', 'expiry_date'];
        
        $result = $this->repo->findAll($request->get_params(), $searchable, $sortable);
        
        // Enrich inventory data with product and warehouse details
        global $wpdb;
        $products_table = $wpdb->prefix . 'wholesale_products';
        $warehouses_table = $wpdb->prefix . 'wholesale_warehouses';
        
        foreach ($result['data'] as &$inv) {
            $inv['product_name'] = $wpdb->get_var($wpdb->prepare("SELECT product_name FROM $products_table WHERE id = %d", $inv['product_id'])) ?: '';
            $inv['sku'] = $wpdb->get_var($wpdb->prepare("SELECT sku FROM $products_table WHERE id = %d", $inv['product_id'])) ?: '';
            $inv['warehouse_name'] = $wpdb->get_var($wpdb->prepare("SELECT warehouse_name FROM $warehouses_table WHERE id = %d", $inv['warehouse_id'])) ?: '';
        }
        return $this->success('Inventory list.', $result);
    }

    public function getInventoryItem(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        if (!$item) {
            return $this->error('Inventory record not found.', [], 404);
        }
        
        global $wpdb;
        $products_table = $wpdb->prefix . 'wholesale_products';
        $warehouses_table = $wpdb->prefix . 'wholesale_warehouses';
        
        $item['product_name'] = $wpdb->get_var($wpdb->prepare("SELECT product_name FROM $products_table WHERE id = %d", $item['product_id'])) ?: '';
        $item['sku'] = $wpdb->get_var($wpdb->prepare("SELECT sku FROM $products_table WHERE id = %d", $item['product_id'])) ?: '';
        $item['warehouse_name'] = $wpdb->get_var($wpdb->prepare("SELECT warehouse_name FROM $warehouses_table WHERE id = %d", $item['warehouse_id'])) ?: '';
        
        return $this->success('Inventory details.', $item);
    }

    public function createInventory(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['product_id']) || empty($p['warehouse_id'])) {
            return $this->error('Product ID and Warehouse ID are required.');
        }
        $data = [
            'product_id'      => (int)$p['product_id'],
            'warehouse_id'    => (int)$p['warehouse_id'],
            'available_stock' => isset($p['available_stock']) ? (int)$p['available_stock'] : 0,
            'reserved_stock'  => isset($p['reserved_stock']) ? (int)$p['reserved_stock'] : 0,
            'damaged_stock'   => isset($p['damaged_stock']) ? (int)$p['damaged_stock'] : 0,
            'minimum_stock'   => isset($p['minimum_stock']) ? (int)$p['minimum_stock'] : 0,
            'batch_number'    => $p['batch_number'] ?? '',
            'expiry_date'     => $p['expiry_date'] ?? null,
        ];
        $formats = ['%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Inventory record created.', ['id' => $id]) : $this->error('Failed to create inventory record.');
    }

    public function updateInventory(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'product_id'      => '%d',
            'warehouse_id'    => '%d',
            'available_stock' => '%d',
            'reserved_stock'  => '%d',
            'damaged_stock'   => '%d',
            'minimum_stock'   => '%d',
            'batch_number'    => '%s',
            'expiry_date'     => '%s',
        ];
        $data = [];
        $formats = [];
        foreach ($fields as $f => $fmt) {
            if (isset($p[$f])) {
                $data[$f] = $p[$f];
                $formats[] = $fmt;
            }
        }
        if (empty($data)) {
            return $this->error('No fields to update.');
        }
        return $this->repo->update($id, $data, $formats) ? $this->success('Inventory updated.') : $this->error('Failed to update inventory.');
    }

    public function deleteInventory(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Inventory deleted.') : $this->error('Failed to delete inventory.');
    }
}
