<?php
namespace ConstructionManagementApi\Controllers;

use WP_REST_Request;

class DashboardController extends BaseController {

    /**
     * GET /dashboard
     */
    public function getDashboardData(WP_REST_Request $request) {
        global $wpdb;

        $prefix = $wpdb->prefix . 'construction_';
        $today = current_time('Y-m-d');
        $this_month = date('m');
        $this_year = date('Y');

        // 1. Active Projects Count
        $active_projects = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$prefix}projects WHERE status = 'Active' AND deleted_at IS NULL");

        // 2. Completed Projects Count
        $completed_projects = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$prefix}projects WHERE status = 'Completed' AND deleted_at IS NULL");

        // 3. Today's Site Expenses
        $today_expenses = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM {$prefix}site_expenses WHERE expense_date = %s AND deleted_at IS NULL",
            $today
        )));

        // 4. Active Labour Count
        $labour_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$prefix}labours WHERE attendance_status = 'PRESENT' AND deleted_at IS NULL");

        // 5. Material Inventory Value
        $inventory_value = floatval($wpdb->get_var("SELECT SUM(available_quantity * purchase_price) FROM {$prefix}materials WHERE deleted_at IS NULL"));

        // 6. Pending Payments (Bills unpaid + Payroll unpaid + Purchases pending)
        $pending_bills = floatval($wpdb->get_var("SELECT SUM(invoice_amount) FROM {$prefix}billing WHERE payment_status = 'PENDING' AND deleted_at IS NULL"));
        $pending_payroll = floatval($wpdb->get_var("SELECT SUM(total_earnings) FROM {$prefix}payroll WHERE payment_status = 'Unpaid' AND deleted_at IS NULL"));
        $pending_purchases = floatval($wpdb->get_var("SELECT SUM(total_amount) FROM {$prefix}purchases WHERE status = 'Pending' AND deleted_at IS NULL"));
        $pending_payments = $pending_bills + $pending_payroll + $pending_purchases;

        // 7. Monthly Revenue (Paid Invoices in the current month)
        $monthly_revenue = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(invoice_amount) FROM {$prefix}billing 
             WHERE payment_status = 'PAID' 
               AND MONTH(invoice_date) = %d 
               AND YEAR(invoice_date) = %d
               AND deleted_at IS NULL",
            $this_month, $this_year
        )));

        // 8. Monthly Expenses (Purchases Approved + Site Expenses + Paid Payroll)
        $monthly_purchase_expenses = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(total_amount) FROM {$prefix}purchases 
             WHERE status = 'Approved' 
               AND MONTH(purchase_date) = %d 
               AND YEAR(purchase_date) = %d
               AND deleted_at IS NULL",
            $this_month, $this_year
        )));

        $monthly_site_expenses = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM {$prefix}site_expenses 
             WHERE MONTH(expense_date) = %d 
               AND YEAR(expense_date) = %d
               AND deleted_at IS NULL",
            $this_month, $this_year
        )));

        $monthly_payroll_expenses = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(total_earnings) FROM {$prefix}payroll 
             WHERE payment_status = 'Paid' 
               AND MONTH(payment_date) = %d 
               AND YEAR(payment_date) = %d
               AND deleted_at IS NULL",
            $this_month, $this_year
        )));

        $monthly_total_expenses = $monthly_purchase_expenses + $monthly_site_expenses + $monthly_payroll_expenses;
        $monthly_profit = $monthly_revenue - $monthly_total_expenses;

        // --- Dashboard Charts Analytics ---

        // Project Progress chart
        $project_progress_query = $wpdb->get_results(
            "SELECT id, project_name, estimated_cost, actual_cost, status, 
                    (SELECT COALESCE(AVG(completion_percentage), 0) FROM {$prefix}milestones WHERE project_id = p.id AND deleted_at IS NULL) as average_progress 
             FROM {$prefix}projects p 
             WHERE deleted_at IS NULL 
             LIMIT 5", 
            ARRAY_A
        );

        $project_progress = [];
        foreach ($project_progress_query as $row) {
            $project_progress[] = [
                'project_name' => $row['project_name'],
                'average_progress' => round(floatval($row['average_progress']), 2),
                'estimated_cost' => floatval($row['estimated_cost']),
                'actual_cost' => floatval($row['actual_cost']),
                'status' => $row['status']
            ];
        }

        // Material consumption values
        $material_consumption_query = $wpdb->get_results(
            "SELECT m.material_name, SUM(p.quantity) as total_qty, SUM(p.total_amount) as total_val 
             FROM {$prefix}purchases p 
             JOIN {$prefix}materials m ON p.material_id = m.id 
             WHERE p.status = 'Approved' AND p.deleted_at IS NULL 
             GROUP BY p.material_id 
             LIMIT 5",
            ARRAY_A
        );
        $material_consumption = [];
        foreach ($material_consumption_query as $row) {
            $material_consumption[] = [
                'material_name' => $row['material_name'],
                'total_quantity' => floatval($row['total_qty']),
                'total_value' => floatval($row['total_val'])
            ];
        }

        // Labour trades count
        $labour_trade_query = $wpdb->get_results(
            "SELECT trade, COUNT(*) as worker_count, SUM(daily_wage) as daily_wage_bill 
             FROM {$prefix}labours 
             WHERE deleted_at IS NULL 
             GROUP BY trade",
            ARRAY_A
        );
        $labour_trade_stats = [];
        foreach ($labour_trade_query as $row) {
            $labour_trade_stats[] = [
                'trade' => $row['trade'],
                'count' => intval($row['worker_count']),
                'daily_bill' => floatval($row['daily_wage_bill'])
            ];
        }

        return $this->success('Dashboard metrics loaded successfully.', [
            'cards' => [
                'active_projects' => $active_projects,
                'completed_projects' => $completed_projects,
                'today_site_expenses' => $today_expenses,
                'labour_headcount' => $labour_count,
                'inventory_value' => $inventory_value,
                'pending_payments' => $pending_payments,
                'monthly_revenue' => $monthly_revenue,
                'monthly_profit' => $monthly_profit
            ],
            'analytics' => [
                'project_progress' => $project_progress,
                'material_consumption' => $material_consumption,
                'labour_productivity' => $labour_trade_stats,
                'monthly_breakdown' => [
                    'revenue' => $monthly_revenue,
                    'expenses' => [
                        'purchases' => $monthly_purchase_expenses,
                        'site_expenses' => $monthly_site_expenses,
                        'payroll' => $monthly_payroll_expenses,
                        'total' => $monthly_total_expenses
                    ],
                    'profit' => $monthly_profit
                ]
            ]
        ]);
    }
}
