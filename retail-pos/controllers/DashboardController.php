<?php
namespace RetailPosApi\Controllers;

use WP_REST_Request;

class DashboardController extends BaseController {

    /**
     * GET /dashboard
     */
    public function getStats(WP_REST_Request $request) {
        global $wpdb;

        $today = current_time('Y-m-d');
        $start_of_month = date('Y-m-01');

        // 1. Core Card Metrics
        $today_sales = (float)$wpdb->get_var(
            $wpdb->prepare("SELECT SUM(total_amount) FROM {$wpdb->prefix}pos_sales WHERE DATE(invoice_date) = %s AND status = 'COMPLETED' AND deleted_at IS NULL", $today)
        ) ?: 0.00;

        $monthly_sales = (float)$wpdb->get_var(
            $wpdb->prepare("SELECT SUM(total_amount) FROM {$wpdb->prefix}pos_sales WHERE DATE(invoice_date) >= %s AND status = 'COMPLETED' AND deleted_at IS NULL", $start_of_month)
        ) ?: 0.00;

        $inventory_value = (float)$wpdb->get_var(
            "SELECT SUM(purchase_price * stock_quantity) FROM {$wpdb->prefix}pos_products WHERE status = 'ACTIVE' AND deleted_at IS NULL"
        ) ?: 0.00;

        $low_stock_count = (int)$wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}pos_inventory WHERE available_stock <= minimum_stock AND deleted_at IS NULL"
        );

        $total_customers = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}pos_customers WHERE status = 'ACTIVE' AND deleted_at IS NULL");
        $total_suppliers = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}pos_suppliers WHERE status = 'ACTIVE' AND deleted_at IS NULL");

        // COGS and Profit today
        $today_sales_revenue = (float)$wpdb->get_var(
            $wpdb->prepare("SELECT SUM(si.selling_price * si.quantity) 
                            FROM {$wpdb->prefix}pos_sale_items si
                            JOIN {$wpdb->prefix}pos_sales s ON si.sale_id = s.id
                            WHERE DATE(s.invoice_date) = %s AND s.status = 'COMPLETED' AND s.deleted_at IS NULL", $today)
        ) ?: 0.00;

        $today_cogs = (float)$wpdb->get_var(
            $wpdb->prepare("SELECT SUM(si.purchase_price * si.quantity) 
                            FROM {$wpdb->prefix}pos_sale_items si
                            JOIN {$wpdb->prefix}pos_sales s ON si.sale_id = s.id
                            WHERE DATE(s.invoice_date) = %s AND s.status = 'COMPLETED' AND s.deleted_at IS NULL", $today)
        ) ?: 0.00;

        $today_discounts = (float)$wpdb->get_var(
            $wpdb->prepare("SELECT SUM(discount) FROM {$wpdb->prefix}pos_sales WHERE DATE(invoice_date) = %s AND status = 'COMPLETED' AND deleted_at IS NULL", $today)
        ) ?: 0.00;

        $today_profit = max(0.00, $today_sales_revenue - $today_cogs - $today_discounts);

        $pending_pos = (int)$wpdb->get_var("SELECT COUNT(DISTINCT po_number) FROM {$wpdb->prefix}pos_purchases WHERE status = 'ORDERED' AND deleted_at IS NULL");

        // 2. Analytical Trends datasets
        // Sales trends (Monthly)
        $sales_trends = $wpdb->get_results(
            "SELECT DATE_FORMAT(invoice_date, '%b %Y') as label, SUM(total_amount) as value 
             FROM {$wpdb->prefix}pos_sales 
             WHERE status = 'COMPLETED' AND deleted_at IS NULL 
             GROUP BY YEAR(invoice_date), MONTH(invoice_date) 
             ORDER BY YEAR(invoice_date) ASC, MONTH(invoice_date) ASC LIMIT 6",
            ARRAY_A
        );

        // Top 5 products sold
        $top_products = $wpdb->get_results(
            "SELECT p.product_name as label, SUM(si.quantity) as value 
             FROM {$wpdb->prefix}pos_sale_items si
             JOIN {$wpdb->prefix}pos_products p ON si.product_id = p.id
             JOIN {$wpdb->prefix}pos_sales s ON si.sale_id = s.id
             WHERE s.status = 'COMPLETED' AND s.deleted_at IS NULL
             GROUP BY si.product_id
             ORDER BY value DESC LIMIT 5",
            ARRAY_A
        );

        // Category performance
        $category_performance = $wpdb->get_results(
            "SELECT c.name as label, SUM(si.total) as value 
             FROM {$wpdb->prefix}pos_sale_items si
             JOIN {$wpdb->prefix}pos_products p ON si.product_id = p.id
             JOIN {$wpdb->prefix}pos_categories c ON p.category_id = c.id
             JOIN {$wpdb->prefix}pos_sales s ON si.sale_id = s.id
             WHERE s.status = 'COMPLETED' AND s.deleted_at IS NULL
             GROUP BY p.category_id
             ORDER BY value DESC LIMIT 5",
            ARRAY_A
        );

        // Store operating expenses totals
        $monthly_expenses = (float)$wpdb->get_var(
            $wpdb->prepare("SELECT SUM(amount) FROM {$wpdb->prefix}pos_expenses WHERE DATE(expense_date) >= %s AND deleted_at IS NULL", $start_of_month)
        ) ?: 0.00;

        return $this->success('Dashboard metrics retrieved.', [
            'cards' => [
                'today_sales' => $today_sales,
                'monthly_sales' => $monthly_sales,
                'inventory_value' => $inventory_value,
                'low_stock_count' => $low_stock_count,
                'total_customers' => $total_customers,
                'total_suppliers' => $total_suppliers,
                'today_profit' => $today_profit,
                'pending_pos' => $pending_pos,
                'monthly_expenses' => $monthly_expenses
            ],
            'charts' => [
                'sales_trends' => $sales_trends ?: [],
                'top_products' => $top_products ?: [],
                'category_performance' => $category_performance ?: []
            ]
        ]);
    }
}
