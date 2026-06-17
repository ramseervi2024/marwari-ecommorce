<?php
namespace AccountingManagementApi\Controllers;

use WP_REST_Request;

class DashboardController extends BaseController {
    
    /**
     * GET /dashboard
     */
    public function getDashboardData(WP_REST_Request $request) {
        global $wpdb;
        
        $table_sales = $wpdb->prefix . 'acc_sales';
        $table_purchases = $wpdb->prefix . 'acc_purchases';
        $table_expenses = $wpdb->prefix . 'acc_expenses';
        $table_gst = $wpdb->prefix . 'acc_gst';
        $table_customers = $wpdb->prefix . 'acc_customers';
        $table_vendors = $wpdb->prefix . 'acc_vendors';
        
        $today = current_time('Y-m-d');
        $start_of_month = date('Y-m-01');
        $end_of_month = date('Y-m-t');

        // 1. KPI Calculations
        $today_sales = (float)$wpdb->get_var($wpdb->prepare(
            "SELECT SUM(total_amount) FROM $table_sales WHERE invoice_date = %s AND deleted_at IS NULL", 
            $today
        )) ?: 0.00;

        $monthly_sales = (float)$wpdb->get_var($wpdb->prepare(
            "SELECT SUM(total_amount) FROM $table_sales WHERE invoice_date BETWEEN %s AND %s AND deleted_at IS NULL", 
            $start_of_month, 
            $end_of_month
        )) ?: 0.00;

        $total_purchases = (float)$wpdb->get_var("SELECT SUM(total_amount) FROM $table_purchases WHERE deleted_at IS NULL") ?: 0.00;
        $total_expenses = (float)$wpdb->get_var("SELECT SUM(amount) FROM $table_expenses WHERE deleted_at IS NULL") ?: 0.00;

        $gst_payable = (float)$wpdb->get_var("SELECT SUM(gst_amount) FROM $table_gst WHERE invoice_type = 'SALES'") ?: 0.00;
        $gst_receivable = (float)$wpdb->get_var("SELECT SUM(gst_amount) FROM $table_gst WHERE invoice_type = 'PURCHASES'") ?: 0.00;

        $outstanding_collections = (float)$wpdb->get_var("SELECT SUM(outstanding_amount) FROM $table_customers WHERE deleted_at IS NULL") ?: 0.00;
        $outstanding_payments = (float)$wpdb->get_var("SELECT SUM(outstanding_amount) FROM $table_vendors WHERE deleted_at IS NULL") ?: 0.00;

        $net_profit = $monthly_sales - ($total_purchases + $total_expenses);

        // 2. Sales Trend (last 6 months)
        $sales_trend = $wpdb->get_results("
            SELECT DATE_FORMAT(invoice_date, '%b %Y') as month, SUM(total_amount) as value 
            FROM $table_sales 
            WHERE deleted_at IS NULL 
            GROUP BY DATE_FORMAT(invoice_date, '%Y-%m') 
            ORDER BY invoice_date ASC 
            LIMIT 6
        ", ARRAY_A);

        // 3. Purchase Trend (last 6 months)
        $purchase_trend = $wpdb->get_results("
            SELECT DATE_FORMAT(purchase_date, '%b %Y') as month, SUM(total_amount) as value 
            FROM $table_purchases 
            WHERE deleted_at IS NULL 
            GROUP BY DATE_FORMAT(purchase_date, '%Y-%m') 
            ORDER BY purchase_date ASC 
            LIMIT 6
        ", ARRAY_A);

        return $this->success('Dashboard metrics loaded successfully.', [
            'cards' => [
                'today_sales' => $today_sales,
                'monthly_sales' => $monthly_sales,
                'total_purchases' => $total_purchases,
                'total_expenses' => $total_expenses,
                'gst_payable' => $gst_payable,
                'gst_receivable' => $gst_receivable,
                'outstanding_collections' => $outstanding_collections,
                'outstanding_payments' => $outstanding_payments,
                'net_profit' => $net_profit
            ],
            'analytics' => [
                'sales_trend' => $sales_trend ?: [],
                'purchase_trend' => $purchase_trend ?: []
            ]
        ]);
    }
}
