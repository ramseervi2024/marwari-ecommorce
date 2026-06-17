<?php
namespace AccountingManagementApi\Controllers;

use WP_REST_Request;

class ReportsController extends BaseController {

    /**
     * GET /reports/profit-loss
     */
    public function getProfitLoss(WP_REST_Request $request) {
        global $wpdb;
        $table_sales = $wpdb->prefix . 'acc_sales';
        $table_purchases = $wpdb->prefix . 'acc_purchases';
        $table_expenses = $wpdb->prefix . 'acc_expenses';

        $revenue = (float)$wpdb->get_var("SELECT SUM(subtotal - discount) FROM $table_sales WHERE deleted_at IS NULL") ?: 0.00;
        $purchases = (float)$wpdb->get_var("SELECT SUM(subtotal) FROM $table_purchases WHERE deleted_at IS NULL") ?: 0.00;
        $expenses = (float)$wpdb->get_var("SELECT SUM(amount) FROM $table_expenses WHERE deleted_at IS NULL") ?: 0.00;

        $gross_profit = $revenue - $purchases;
        $net_profit = $gross_profit - $expenses;

        return $this->success('Profit and Loss financial report retrieved.', [
            'total_revenue' => $revenue,
            'cost_of_goods_sold' => $purchases,
            'gross_profit' => $gross_profit,
            'operating_expenses' => $expenses,
            'net_profit' => $net_profit
        ]);
    }

    /**
     * GET /reports/balance-sheet
     */
    public function getBalanceSheet(WP_REST_Request $request) {
        global $wpdb;
        $table_accounts = $wpdb->prefix . 'acc_accounts';

        $assets = $wpdb->get_results("SELECT account_code, account_name, balance FROM $table_accounts WHERE account_type = 'Asset' AND deleted_at IS NULL", ARRAY_A) ?: [];
        $liabilities = $wpdb->get_results("SELECT account_code, account_name, balance FROM $table_accounts WHERE account_type = 'Liability' AND deleted_at IS NULL", ARRAY_A) ?: [];
        $equity = $wpdb->get_results("SELECT account_code, account_name, balance FROM $table_accounts WHERE account_type = 'Equity' AND deleted_at IS NULL", ARRAY_A) ?: [];

        $total_assets = array_sum(array_column($assets, 'balance'));
        $total_liabilities = array_sum(array_column($liabilities, 'balance'));
        $total_equity = array_sum(array_column($equity, 'balance'));

        return $this->success('Balance Sheet report retrieved.', [
            'assets' => [
                'items' => $assets,
                'total' => $total_assets
            ],
            'liabilities' => [
                'items' => $liabilities,
                'total' => $total_liabilities
            ],
            'equity' => [
                'items' => $equity,
                'total' => $total_equity
            ]
        ]);
    }

    /**
     * GET /reports/gst-summary
     */
    public function getGstSummary(WP_REST_Request $request) {
        global $wpdb;
        $table_gst = $wpdb->prefix . 'acc_gst';

        $summary = $wpdb->get_results("
            SELECT tax_period, invoice_type, gst_type, SUM(taxable_amount) as total_taxable, SUM(gst_amount) as total_gst 
            FROM $table_gst 
            GROUP BY tax_period, invoice_type, gst_type
            ORDER BY tax_period DESC
        ", ARRAY_A) ?: [];

        return $this->success('GST returns summary report retrieved.', [
            'summary' => $summary
        ]);
    }
}
