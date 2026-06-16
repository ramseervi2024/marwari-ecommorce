<?php
namespace RetailPosApi\Controllers;

use WP_REST_Request;

class ReportsController extends BaseController {

    /**
     * GET /reports/sales
     */
    public function getSales(WP_REST_Request $request) {
        global $wpdb;
        
        $method_breakdown = $wpdb->get_results(
            "SELECT payment_method as label, SUM(total_amount) as value, COUNT(*) as count
             FROM {$wpdb->prefix}pos_sales 
             WHERE status = 'COMPLETED' AND deleted_at IS NULL 
             GROUP BY payment_method",
            ARRAY_A
        );

        $monthly_sales = $wpdb->get_results(
            "SELECT DATE_FORMAT(invoice_date, '%b %Y') as label, SUM(total_amount) as value, COUNT(*) as count
             FROM {$wpdb->prefix}pos_sales 
             WHERE status = 'COMPLETED' AND deleted_at IS NULL 
             GROUP BY YEAR(invoice_date), MONTH(invoice_date)
             ORDER BY YEAR(invoice_date) ASC, MONTH(invoice_date) ASC",
            ARRAY_A
        );

        return $this->success('Sales reports compiled.', [
            'payment_methods' => $method_breakdown ?: [],
            'monthly_history' => $monthly_sales ?: []
        ]);
    }

    /**
     * GET /reports/gst
     */
    public function getGst(WP_REST_Request $request) {
        global $wpdb;

        $monthly_gst = $wpdb->get_results(
            "SELECT DATE_FORMAT(invoice_date, '%b %Y') as month, 
                    SUM(subtotal) as taxable_amount, 
                    SUM(gst_amount) as gst_total, 
                    SUM(total_amount) as invoice_total 
             FROM {$wpdb->prefix}pos_sales 
             WHERE status = 'COMPLETED' AND deleted_at IS NULL 
             GROUP BY YEAR(invoice_date), MONTH(invoice_date)
             ORDER BY YEAR(invoice_date) ASC, MONTH(invoice_date) ASC",
            ARRAY_A
        );

        foreach ($monthly_gst as &$row) {
            $gst = floatval($row['gst_total']);
            // Standard Indian GST splits: CGST (50%) & SGST (50%) for local sales
            $row['cgst'] = $gst / 2;
            $row['sgst'] = $gst / 2;
            $row['igst'] = 0.00; // Simplified
        }

        return $this->success('GST reports compiled.', $monthly_gst ?: []);
    }

    /**
     * GET /reports/profit-loss
     */
    public function getProfitLoss(WP_REST_Request $request) {
        global $wpdb;

        $monthly_data = $wpdb->get_results(
            "SELECT DATE_FORMAT(s.invoice_date, '%b %Y') as month,
                    SUM(si.selling_price * si.quantity) as sales_revenue,
                    SUM(si.purchase_price * si.quantity) as cogs,
                    SUM(s.discount) as discounts
             FROM {$wpdb->prefix}pos_sales s
             JOIN {$wpdb->prefix}pos_sale_items si ON s.id = si.sale_id
             WHERE s.status = 'COMPLETED' AND s.deleted_at IS NULL
             GROUP BY YEAR(s.invoice_date), MONTH(s.invoice_date)
             ORDER BY YEAR(s.invoice_date) ASC, MONTH(s.invoice_date) ASC",
            ARRAY_A
        );

        // Fetch monthly expenses
        $expenses = $wpdb->get_results(
            "SELECT DATE_FORMAT(expense_date, '%b %Y') as month, SUM(amount) as amount 
             FROM {$wpdb->prefix}pos_expenses 
             WHERE deleted_at IS NULL 
             GROUP BY YEAR(expense_date), MONTH(expense_date)",
            ARRAY_A
        );

        $exp_map = [];
        foreach ($expenses as $e) {
            $exp_map[$e['month']] = floatval($e['amount']);
        }

        $report = [];
        foreach ($monthly_data as $row) {
            $m = $row['month'];
            $rev = floatval($row['sales_revenue']);
            $cogs = floatval($row['cogs']);
            $disc = floatval($row['discounts']);
            $exp = $exp_map[$m] ?? 0.00;

            $gross_profit = $rev - $cogs - $disc;
            $net_profit = $gross_profit - $exp;

            $report[] = [
                'month' => $m,
                'sales_revenue' => $rev,
                'cost_of_goods' => $cogs,
                'discounts' => $disc,
                'expenses' => $exp,
                'gross_profit' => $gross_profit,
                'net_profit' => $net_profit
            ];
        }

        return $this->success('Profit and loss reports compiled.', $report);
    }
}
