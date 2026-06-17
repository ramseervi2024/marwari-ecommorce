<?php
namespace InventoryManagementApi\Controllers;

use WP_REST_Request;

class DashboardController extends BaseController {

    /**
     * GET /dashboard
     */
    public function getDashboardData(WP_REST_Request $request) {
        global $wpdb;

        $table_products = $wpdb->prefix . 'inv_products';
        $table_warehouses = $wpdb->prefix . 'inv_warehouses';
        $table_stock = $wpdb->prefix . 'inv_stock';
        $table_pos = $wpdb->prefix . 'inv_purchase_orders';
        $table_transfers = $wpdb->prefix . 'inv_transfers';
        $table_audits = $wpdb->prefix . 'inv_audits';

        // 1. KPI Calculations
        $total_products = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_products WHERE deleted_at IS NULL AND status = 'ACTIVE'");
        $total_warehouses = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_warehouses WHERE deleted_at IS NULL AND status = 'ACTIVE'");

        // Stock Value (sum of available_stock * purchase_price)
        $total_stock_value = (float)$wpdb->get_var("
            SELECT SUM(s.available_stock * p.purchase_price) 
            FROM $table_stock s
            JOIN $table_products p ON s.product_id = p.id
            WHERE p.deleted_at IS NULL
        ") ?: 0.00;

        // Low stock count (products where total stock across all warehouses < minimum_stock)
        $low_stock_items = (int)$wpdb->get_var("
            SELECT COUNT(*) FROM (
                SELECT p.id, COALESCE(SUM(s.available_stock), 0) as total_qty, p.minimum_stock
                FROM $table_products p
                LEFT JOIN $table_stock s ON p.id = s.product_id
                WHERE p.deleted_at IS NULL AND p.status = 'ACTIVE'
                GROUP BY p.id
                HAVING total_qty < p.minimum_stock
            ) as temp
        ");

        $purchase_orders_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_pos WHERE deleted_at IS NULL");
        $pending_transfers_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_transfers WHERE status = 'Pending' AND deleted_at IS NULL");
        
        $total_damaged_stock = (int)$wpdb->get_var("SELECT SUM(damaged_stock) FROM $table_stock") ?: 0;
        $pending_audits_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_audits WHERE status = 'Pending' AND deleted_at IS NULL");

        // 2. Warehouse Utilization (Stock grouped by warehouse)
        $warehouse_utilization = $wpdb->get_results("
            SELECT w.warehouse_name, COALESCE(SUM(s.available_stock), 0) as total_stock 
            FROM $table_warehouses w 
            LEFT JOIN $table_stock s ON w.id = s.warehouse_id 
            WHERE w.deleted_at IS NULL AND w.status = 'ACTIVE'
            GROUP BY w.id
        ", ARRAY_A);

        // 3. Purchase order totals trend (last 6 months)
        $purchase_trends = $wpdb->get_results("
            SELECT DATE_FORMAT(order_date, '%b %Y') as month, SUM(total_amount) as total_val 
            FROM $table_pos 
            WHERE deleted_at IS NULL 
            GROUP BY DATE_FORMAT(order_date, '%Y-%m') 
            ORDER BY order_date ASC 
            LIMIT 6
        ", ARRAY_A);

        return $this->success('Dashboard metrics loaded successfully.', [
            'cards' => [
                'total_products' => $total_products,
                'total_warehouses' => $total_warehouses,
                'total_stock_value' => $total_stock_value,
                'low_stock_items' => $low_stock_items,
                'purchase_orders' => $purchase_orders_count,
                'pending_transfers' => $pending_transfers_count,
                'damaged_stock' => $total_damaged_stock,
                'pending_audits' => $pending_audits_count
            ],
            'analytics' => [
                'warehouse_utilization' => $warehouse_utilization ?: [],
                'purchase_trends' => $purchase_trends ?: []
            ]
        ]);
    }
}
