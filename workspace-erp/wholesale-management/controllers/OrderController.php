<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\OrderRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class OrderController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new OrderRepository();
    }

    public function getOrders(WP_REST_Request $request) {
        $searchable = ['order_number', 'order_status'];
        $sortable = ['id', 'order_number', 'order_date', 'total_amount', 'net_amount', 'created_at'];
        $result = $this->repo->findAll($request->get_params(), $searchable, $sortable);
        
        // Enrich orders with dealer name
        global $wpdb;
        $dealers_table = $wpdb->prefix . 'wholesale_dealers';
        foreach ($result['data'] as &$ord) {
            $ord['dealer_name'] = $wpdb->get_var($wpdb->prepare("SELECT dealer_name FROM $dealers_table WHERE id = %d", $ord['dealer_id'])) ?: '';
        }
        return $this->success('Orders list.', $result);
    }

    public function getOrder(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        if (!$item) {
            return $this->error('Order not found.', [], 404);
        }
        
        global $wpdb;
        $dealers_table = $wpdb->prefix . 'wholesale_dealers';
        $item['dealer_name'] = $wpdb->get_var($wpdb->prepare("SELECT dealer_name FROM $dealers_table WHERE id = %d", $item['dealer_id'])) ?: '';
        $item['items'] = $this->repo->getItems($id);
        return $this->success('Order details.', $item);
    }

    public function createOrder(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['dealer_id']) || empty($p['order_date'])) {
            return $this->error('Dealer ID and order date are required.');
        }

        $order_number = $p['order_number'] ?? $this->repo->generateCode('ORD-', 'order_number');
        
        $data = [
            'order_number'    => $order_number,
            'dealer_id'       => (int)$p['dealer_id'],
            'sales_rep_id'    => !empty($p['sales_rep_id']) ? (int)$p['sales_rep_id'] : null,
            'order_date'      => $p['order_date'],
            'total_amount'    => isset($p['total_amount']) ? (float)$p['total_amount'] : 0.00,
            'discount_amount' => isset($p['discount_amount']) ? (float)$p['discount_amount'] : 0.00,
            'gst_amount'      => isset($p['gst_amount']) ? (float)$p['gst_amount'] : 0.00,
            'net_amount'      => isset($p['net_amount']) ? (float)$p['net_amount'] : 0.00,
            'order_status'    => $p['order_status'] ?? 'Draft',
            'notes'           => $p['notes'] ?? '',
        ];
        $formats = ['%s', '%d', '%d', '%s', '%f', '%f', '%f', '%f', '%s', '%s'];
        
        $id = $this->repo->create($data, $formats);
        if (!$id) {
            return $this->error('Failed to create order.');
        }

        if (!empty($p['items']) && is_array($p['items'])) {
            $this->repo->saveItems($id, $p['items']);
        }
        
        return $this->success('Order created.', ['id' => $id, 'order_number' => $order_number]);
    }

    public function updateOrder(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        
        $fields = [
            'dealer_id'       => '%d',
            'sales_rep_id'    => '%d',
            'order_date'      => '%s',
            'total_amount'    => '%f',
            'discount_amount' => '%f',
            'gst_amount'      => '%f',
            'net_amount'      => '%f',
            'order_status'    => '%s',
            'notes'           => '%s',
        ];
        $data = [];
        $formats = [];
        foreach ($fields as $f => $fmt) {
            if (isset($p[$f])) {
                $data[$f] = $p[$f];
                $formats[] = $fmt;
            }
        }
        
        if (!empty($data)) {
            $this->repo->update($id, $data, $formats);
        }
        
        if (isset($p['items']) && is_array($p['items'])) {
            $this->repo->saveItems($id, $p['items']);
        }
        
        return $this->success('Order updated.');
    }

    public function deleteOrder(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Order deleted.') : $this->error('Failed to delete order.');
    }
}
