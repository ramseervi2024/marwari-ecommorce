<?php
namespace WholesaleErp\Controllers;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class ReportsController extends BaseController {
    public function getDealersReport(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;
        $sql = "SELECT d.dealer_code, d.dealer_name, d.mobile, d.email, d.city, d.credit_limit, d.available_credit, d.status,
                       COALESCE((SELECT SUM(o.balance) FROM {$p}wholesale_outstandings o WHERE o.dealer_id = d.id AND o.status != 'Paid' AND o.deleted_at IS NULL), 0) as total_outstanding
                FROM {$p}wholesale_dealers d
                WHERE d.deleted_at IS NULL";
        $results = $wpdb->get_results($sql, ARRAY_A);
        return $this->success('Dealers report.', $results ?: []);
    }

    public function getOrdersReport(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;
        $sql = "SELECT o.order_number, o.order_date, o.net_amount, o.order_status, d.dealer_name, s.full_name as sales_rep_name
                FROM {$p}wholesale_orders o
                LEFT JOIN {$p}wholesale_dealers d ON d.id = o.dealer_id
                LEFT JOIN {$p}wholesale_sales_reps s ON s.id = o.sales_rep_id
                WHERE o.deleted_at IS NULL
                ORDER BY o.order_date DESC";
        $results = $wpdb->get_results($sql, ARRAY_A);
        return $this->success('Orders report.', $results ?: []);
    }

    public function getSalesReport(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;
        $sql = "SELECT order_date, COUNT(id) as total_orders, SUM(net_amount) as total_sales, SUM(gst_amount) as total_gst
                FROM {$p}wholesale_orders
                WHERE deleted_at IS NULL
                GROUP BY order_date
                ORDER BY order_date DESC LIMIT 30";
        $results = $wpdb->get_results($sql, ARRAY_A);
        return $this->success('Sales report.', $results ?: []);
    }

    public function getCollectionsReport(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;
        $sql = "SELECT py.receipt_number, py.payment_date, py.amount, py.payment_method, py.reference_number, d.dealer_name
                FROM {$p}wholesale_payments py
                LEFT JOIN {$p}wholesale_dealers d ON d.id = py.dealer_id
                WHERE py.deleted_at IS NULL
                ORDER BY py.payment_date DESC";
        $results = $wpdb->get_results($sql, ARRAY_A);
        return $this->success('Collections report.', $results ?: []);
    }

    public function getOutstandingReport(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;
        $sql = "SELECT ot.invoice_number, ot.invoice_date, ot.due_date, ot.amount, ot.paid_amount, ot.balance, ot.days_overdue, ot.status, d.dealer_name
                FROM {$p}wholesale_outstandings ot
                LEFT JOIN {$p}wholesale_dealers d ON d.id = ot.dealer_id
                WHERE ot.deleted_at IS NULL AND ot.status != 'Paid'
                ORDER BY ot.due_date ASC";
        $results = $wpdb->get_results($sql, ARRAY_A);
        return $this->success('Outstanding report.', $results ?: []);
    }

    public function getInventoryReport(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;
        $sql = "SELECT i.available_stock, i.reserved_stock, i.damaged_stock, i.minimum_stock, i.batch_number, pr.product_name, pr.sku, w.warehouse_name
                FROM {$p}wholesale_inventory i
                LEFT JOIN {$p}wholesale_products pr ON pr.id = i.product_id
                LEFT JOIN {$p}wholesale_warehouses w ON w.id = i.warehouse_id
                WHERE i.deleted_at IS NULL";
        $results = $wpdb->get_results($sql, ARRAY_A);
        return $this->success('Inventory report.', $results ?: []);
    }

    public function getDispatchesReport(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;
        $sql = "SELECT ds.dispatch_number, ds.vehicle_number, ds.driver_name, ds.dispatch_date, ds.expected_delivery_date, ds.status, o.order_number, d.dealer_name
                FROM {$p}wholesale_dispatches ds
                LEFT JOIN {$p}wholesale_orders o ON o.id = ds.order_id
                LEFT JOIN {$p}wholesale_dealers d ON d.id = o.dealer_id
                WHERE ds.deleted_at IS NULL";
        $results = $wpdb->get_results($sql, ARRAY_A);
        return $this->success('Dispatches report.', $results ?: []);
    }

    public function getGstReport(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;
        $sql = "SELECT o.order_number, o.order_date, o.total_amount as taxable_amount, o.gst_amount, o.net_amount, d.dealer_name, d.gst_number
                FROM {$p}wholesale_orders o
                LEFT JOIN {$p}wholesale_dealers d ON d.id = o.dealer_id
                WHERE o.deleted_at IS NULL AND o.gst_amount > 0
                ORDER BY o.order_date DESC";
        $results = $wpdb->get_results($sql, ARRAY_A);
        return $this->success('GST tax report.', $results ?: []);
    }

    public function getTargetsReport(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;
        $sql = "SELECT employee_code, full_name, territory, target_amount, achieved_amount, (target_amount - achieved_amount) as pending_amount, status
                FROM {$p}wholesale_sales_reps
                WHERE deleted_at IS NULL";
        $results = $wpdb->get_results($sql, ARRAY_A);
        return $this->success('Sales targets report.', $results ?: []);
    }

    public function getProfitLossReport(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;
        
        // Calculate total sales revenue, total purchase cost, and profit margin
        $total_sales = $wpdb->get_var("SELECT COALESCE(SUM(net_amount), 0) FROM {$p}wholesale_orders WHERE order_status='Delivered' AND deleted_at IS NULL");
        $total_purchases = $wpdb->get_var("SELECT COALESCE(SUM(net_amount), 0) FROM {$p}wholesale_purchases WHERE status='Completed' AND deleted_at IS NULL");
        
        // Detailed list of item-wise revenue vs purchase costs for margin tracking
        $items_sql = "SELECT oi.quantity, oi.unit_price as sale_price, pr.purchase_price, pr.product_name, pr.sku,
                             (oi.quantity * oi.unit_price) as total_sale_value,
                             (oi.quantity * pr.purchase_price) as total_cost_value,
                             ((oi.quantity * oi.unit_price) - (oi.quantity * pr.purchase_price)) as estimated_margin
                      FROM {$p}wholesale_order_items oi
                      JOIN {$p}wholesale_orders o ON o.id = oi.order_id
                      JOIN {$p}wholesale_products pr ON pr.id = oi.product_id
                      WHERE o.deleted_at IS NULL";
        $items_data = $wpdb->get_results($items_sql, ARRAY_A);
        
        return $this->success('Profit & Loss summary.', [
            'total_sales'     => (float)$total_sales,
            'total_purchases' => (float)$total_purchases,
            'gross_profit'    => (float)($total_sales - $total_purchases),
            'item_margins'    => $items_data ?: []
        ]);
    }
}
