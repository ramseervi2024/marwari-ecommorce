<?php
namespace WholesaleErp\Controllers;
use WP_REST_Request;
use WholesaleErp\Repositories\OrderRepository;
use WholesaleErp\Repositories\PaymentRepository;

if (!defined('ABSPATH')) exit;

class PortalController extends BaseController {
    private function getDealer() {
        $uid = get_current_user_id();
        if (!$uid) return null;
        $user = get_userdata($uid);
        if (!$user) return null;
        
        global $wpdb;
        $p = $wpdb->prefix;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$p}wholesale_dealers WHERE email = %s AND deleted_at IS NULL", $user->user_email), ARRAY_A);
    }

    public function getDashboard(WP_REST_Request $request) {
        $dealer = $this->getDealer();
        if (!$dealer) {
            return $this->error('Dealer profile not found for this account.', [], 404);
        }

        global $wpdb;
        $p = $wpdb->prefix;
        
        $outstanding = $wpdb->get_var($wpdb->prepare("SELECT COALESCE(SUM(balance), 0) FROM {$p}wholesale_outstandings WHERE dealer_id = %d AND status != 'Paid' AND deleted_at IS NULL", $dealer['id']));
        $orders_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$p}wholesale_orders WHERE dealer_id = %d AND deleted_at IS NULL", $dealer['id']));
        
        // Recent orders
        $recent = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$p}wholesale_orders WHERE dealer_id = %d AND deleted_at IS NULL ORDER BY id DESC LIMIT 5", $dealer['id']), ARRAY_A);

        return $this->success('Dealer dashboard.', [
            'dealer' => [
                'id'               => $dealer['id'],
                'dealer_code'      => $dealer['dealer_code'],
                'dealer_name'      => $dealer['dealer_name'],
                'credit_limit'     => (float)$dealer['credit_limit'],
                'available_credit' => (float)$dealer['available_credit'],
            ],
            'stats' => [
                'outstanding_amount' => (float)$outstanding,
                'total_orders'       => (int)$orders_count,
            ],
            'recent_orders' => $recent ?: [],
        ]);
    }

    public function getOrders(WP_REST_Request $request) {
        $dealer = $this->getDealer();
        if (!$dealer) return $this->error('Dealer profile not found.', [], 404);

        global $wpdb;
        $p = $wpdb->prefix;
        $orders = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$p}wholesale_orders WHERE dealer_id = %d AND deleted_at IS NULL ORDER BY id DESC", $dealer['id']), ARRAY_A);
        return $this->success('Dealer orders.', $orders ?: []);
    }

    public function createOrder(WP_REST_Request $request) {
        $dealer = $this->getDealer();
        if (!$dealer) return $this->error('Dealer profile not found.', [], 404);

        $p = $request->get_json_params();
        $orderRepo = new OrderRepository();
        $order_number = $orderRepo->generateCode('ORD-', 'order_number');
        
        $data = [
            'order_number'    => $order_number,
            'dealer_id'       => $dealer['id'],
            'order_date'      => current_time('Y-m-d'),
            'total_amount'    => isset($p['total_amount']) ? (float)$p['total_amount'] : 0.00,
            'discount_amount' => isset($p['discount_amount']) ? (float)$p['discount_amount'] : 0.00,
            'gst_amount'      => isset($p['gst_amount']) ? (float)$p['gst_amount'] : 0.00,
            'net_amount'      => isset($p['net_amount']) ? (float)$p['net_amount'] : 0.00,
            'order_status'    => 'Draft',
            'notes'           => $p['notes'] ?? '',
        ];
        
        $id = $orderRepo->create($data, ['%s', '%d', '%s', '%f', '%f', '%f', '%f', '%s', '%s']);
        if (!$id) return $this->error('Failed to place order.');

        if (!empty($p['items']) && is_array($p['items'])) {
            $orderRepo->saveItems($id, $p['items']);
        }

        return $this->success('Order placed successfully.', ['id' => $id, 'order_number' => $order_number]);
    }

    public function getPayments(WP_REST_Request $request) {
        $dealer = $this->getDealer();
        if (!$dealer) return $this->error('Dealer profile not found.', [], 404);

        global $wpdb;
        $p = $wpdb->prefix;
        $payments = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$p}wholesale_payments WHERE dealer_id = %d AND deleted_at IS NULL ORDER BY id DESC", $dealer['id']), ARRAY_A);
        return $this->success('Dealer payments.', $payments ?: []);
    }

    public function getInvoices(WP_REST_Request $request) {
        $dealer = $this->getDealer();
        if (!$dealer) return $this->error('Dealer profile not found.', [], 404);

        global $wpdb;
        $p = $wpdb->prefix;
        $invoices = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$p}wholesale_billing WHERE dealer_id = %d AND deleted_at IS NULL ORDER BY id DESC", $dealer['id']), ARRAY_A);
        return $this->success('Dealer invoices.', $invoices ?: []);
    }
}
