<?php
namespace RestaurantManagementApi\Controllers;

use WP_REST_Request;

class ReportsController extends BaseController {
    
    public function getSalesReport(WP_REST_Request $request) {
        global $wpdb;
        $t_invoices = $wpdb->prefix . 'restaurant_invoices';
        
        $start_date = sanitize_text_field($request->get_param('start_date') ?: date('Y-m-d', strtotime('-30 days')));
        $end_date = sanitize_text_field($request->get_param('end_date') ?: date('Y-m-d'));

        $query = $wpdb->prepare(
            "SELECT DATE(created_at) as sale_date, COUNT(*) as invoice_count, SUM(subtotal) as subtotal, SUM(discount) as discount, SUM(tax) as tax, SUM(total_amount) as total_revenue
             FROM {$t_invoices} 
             WHERE DATE(created_at) >= %s AND DATE(created_at) <= %s AND status = 'Paid'
             GROUP BY DATE(created_at)
             ORDER BY DATE(created_at) DESC",
            $start_date, $end_date
        );

        $results = $wpdb->get_results($query, ARRAY_A);
        return $this->success('Sales report generated successfully.', $results);
    }

    public function getMenuItemsReport(WP_REST_Request $request) {
        global $wpdb;
        $t_order_items = $wpdb->prefix . 'restaurant_order_items';
        $t_menu = $wpdb->prefix . 'restaurant_menu';

        $query = "SELECT m.id, m.item_code, m.item_name, SUM(oi.quantity) as total_qty_sold, SUM(oi.total) as total_revenue
                  FROM {$t_order_items} oi
                  JOIN {$t_menu} m ON oi.menu_item_id = m.id 
                  GROUP BY oi.menu_item_id
                  ORDER BY total_qty_sold DESC";

        $results = $wpdb->get_results($query, ARRAY_A);
        return $this->success('Menu items performance report generated successfully.', $results);
    }

    public function getInventoryReport(WP_REST_Request $request) {
        global $wpdb;
        $t_ingredients = $wpdb->prefix . 'restaurant_ingredients';
        $t_suppliers = $wpdb->prefix . 'restaurant_suppliers';

        $query = "SELECT i.*, s.supplier_name 
                  FROM {$t_ingredients} i
                  LEFT JOIN {$t_suppliers} s ON i.supplier_id = s.id 
                  ORDER BY i.current_stock ASC";

        $results = $wpdb->get_results($query, ARRAY_A);
        return $this->success('Inventory levels report generated successfully.', $results);
    }

    public function getProfitLossReport(WP_REST_Request $request) {
        global $wpdb;
        $t_invoices = $wpdb->prefix . 'restaurant_invoices';
        $t_expenses = $wpdb->prefix . 'restaurant_expenses';
        $t_purchases = $wpdb->prefix . 'restaurant_purchases';

        $start_date = sanitize_text_field($request->get_param('start_date') ?: date('Y-m-d', strtotime('-30 days')));
        $end_date = sanitize_text_field($request->get_param('end_date') ?: date('Y-m-d'));

        // Revenue
        $revenue = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(total_amount) FROM {$t_invoices} WHERE DATE(created_at) >= %s AND DATE(created_at) <= %s AND status = 'Paid'",
            $start_date, $end_date
        )));

        // Overhead Expenses
        $overhead = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM {$t_expenses} WHERE DATE(created_at) >= %s AND DATE(created_at) <= %s",
            $start_date, $end_date
        )));

        // Ingredient Restocking Purchases
        $restock = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(total_amount) FROM {$t_purchases} WHERE DATE(created_at) >= %s AND DATE(created_at) <= %s",
            $start_date, $end_date
        )));

        $total_expenses = $overhead + $restock;
        $net_profit = $revenue - $total_expenses;

        return $this->success('Profit & Loss statement generated successfully.', [
            'revenue' => $revenue,
            'expenses_overhead' => $overhead,
            'expenses_restock' => $restock,
            'total_expenses' => $total_expenses,
            'net_profit' => $net_profit,
            'profit_margin_percentage' => $revenue > 0 ? round(($net_profit / $revenue) * 100, 2) : 0
        ]);
    }
}
