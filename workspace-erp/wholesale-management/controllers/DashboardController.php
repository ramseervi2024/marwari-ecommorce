<?php
namespace WholesaleErp\Controllers;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class DashboardController extends BaseController {
    public function getStats(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;

        $total_dealers      = $wpdb->get_var("SELECT COUNT(*) FROM {$p}wholesale_dealers WHERE status='Active' AND deleted_at IS NULL");
        $today_orders       = $wpdb->get_var("SELECT COUNT(*) FROM {$p}wholesale_orders WHERE order_date = CURDATE() AND deleted_at IS NULL");
        $pending_deliveries = $wpdb->get_var("SELECT COUNT(*) FROM {$p}wholesale_dispatches WHERE status='Pending' AND deleted_at IS NULL");
        $outstanding_amount = $wpdb->get_var("SELECT COALESCE(SUM(balance), 0) FROM {$p}wholesale_outstandings WHERE status != 'Paid' AND deleted_at IS NULL");
        $available_stock    = $wpdb->get_var("SELECT COALESCE(SUM(available_stock), 0) FROM {$p}wholesale_inventory WHERE deleted_at IS NULL");
        
        $monthly_sales      = $wpdb->get_var("SELECT COALESCE(SUM(net_amount), 0) FROM {$p}wholesale_orders WHERE MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE()) AND deleted_at IS NULL");
        $collections        = $wpdb->get_var("SELECT COALESCE(SUM(amount), 0) FROM {$p}wholesale_payments WHERE MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE()) AND deleted_at IS NULL");
        $credit_used        = $wpdb->get_var("SELECT COALESCE(SUM(used_credit), 0) FROM {$p}wholesale_credit_limits WHERE deleted_at IS NULL");
        $credit_limit       = $wpdb->get_var("SELECT COALESCE(SUM(credit_limit), 0) FROM {$p}wholesale_credit_limits WHERE deleted_at IS NULL");
        
        $credit_utilization = 0;
        if ($credit_limit > 0) {
            $credit_utilization = round(($credit_used / $credit_limit) * 100, 2);
        }

        // Recent orders
        $recent_orders = $wpdb->get_results("SELECT o.*, d.dealer_name FROM {$p}wholesale_orders o JOIN {$p}wholesale_dealers d ON d.id=o.dealer_id WHERE o.deleted_at IS NULL ORDER BY o.id DESC LIMIT 5", ARRAY_A);

        // Low stock items
        $low_stock = $wpdb->get_results("SELECT i.*, p.product_name, p.sku, w.warehouse_name FROM {$p}wholesale_inventory i JOIN {$p}wholesale_products p ON p.id=i.product_id JOIN {$p}wholesale_warehouses w ON w.id=i.warehouse_id WHERE i.available_stock <= i.minimum_stock AND i.deleted_at IS NULL LIMIT 5", ARRAY_A);

        return $this->success('Dashboard metrics.', [
            'summary' => [
                'total_dealers'      => (int)$total_dealers,
                'today_orders'       => (int)$today_orders,
                'pending_deliveries' => (int)$pending_deliveries,
                'outstanding_amount' => (float)$outstanding_amount,
                'available_stock'    => (int)$available_stock,
                'monthly_sales'      => (float)$monthly_sales,
                'collections'        => (float)$collections,
                'credit_utilization' => (float)$credit_utilization,
            ],
            'recent_orders' => $recent_orders ?: [],
            'low_stock'     => $low_stock ?: [],
        ]);
    }
}
