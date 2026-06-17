<?php
namespace ServiceManagementApi\Controllers;

use WP_REST_Request;

class DashboardController extends BaseController {

    /**
     * GET /dashboard
     */
    public function getDashboardData(WP_REST_Request $request) {
        global $wpdb;

        $table_leads = $wpdb->prefix . 'ser_leads';
        $table_quotations = $wpdb->prefix . 'ser_quotations';
        $table_jobs = $wpdb->prefix . 'ser_jobs';
        $table_amc = $wpdb->prefix . 'ser_amc';
        $table_invoices = $wpdb->prefix . 'ser_invoices';
        $table_payments = $wpdb->prefix . 'ser_payments';

        // 1. KPI Calculations
        $total_leads = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_leads WHERE deleted_at IS NULL");
        $pending_quotes = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_quotations WHERE deleted_at IS NULL AND status IN ('Draft', 'Sent')");
        
        $active_jobs = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_jobs WHERE deleted_at IS NULL AND status IN ('Scheduled', 'In Progress')");
        
        // Technicians can only see their own assigned jobs
        if (current_user_can('view_assigned_jobs') && !current_user_can('manage_jobs')) {
            $tech_id = get_current_user_id();
            $active_jobs = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_jobs WHERE deleted_at IS NULL AND status IN ('Scheduled', 'In Progress') AND technician_id = %d", $tech_id));
        }

        $active_amc = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_amc WHERE deleted_at IS NULL AND status = 'Active'");
        
        $total_invoiced = (float)$wpdb->get_var("SELECT SUM(total_amount) FROM $table_invoices WHERE deleted_at IS NULL") ?: 0.00;
        $total_payments = (float)$wpdb->get_var("SELECT SUM(amount) FROM $table_payments") ?: 0.00;
        
        $pending_receivables = max(0, $total_invoiced - $total_payments);

        // 2. Technician Load (Number of jobs grouped by technician)
        $users = get_users([
            'role__in' => ['service_technician', 'service_super_admin', 'service_manager', 'administrator']
        ]);
        
        $technician_load = [];
        foreach ($users as $u) {
            $job_count = (int)$wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM $table_jobs 
                WHERE technician_id = %d AND status IN ('Scheduled', 'In Progress') AND deleted_at IS NULL
            ", $u->ID));
            if ($job_count > 0 || in_array('service_technician', $u->roles)) {
                $technician_load[] = [
                    'name' => $u->display_name ?: $u->user_login,
                    'jobs_count' => $job_count
                ];
            }
        }

        // 3. Monthly payment receipt trends
        $payment_trends = $wpdb->get_results("
            SELECT DATE_FORMAT(payment_date, '%b %Y') as month, SUM(amount) as total_val 
            FROM $table_payments 
            GROUP BY DATE_FORMAT(payment_date, '%Y-%m') 
            ORDER BY payment_date ASC 
            LIMIT 6
        ", ARRAY_A);

        return $this->success('Dashboard metrics loaded successfully.', [
            'cards' => [
                'total_leads' => $total_leads,
                'pending_quotes' => $pending_quotes,
                'active_jobs' => $active_jobs,
                'active_amc' => $active_amc,
                'total_invoiced' => $total_invoiced,
                'total_payments' => $total_payments,
                'pending_receivables' => $pending_receivables
            ],
            'analytics' => [
                'technician_load' => $technician_load,
                'payment_trends' => $payment_trends ?: []
            ]
        ]);
    }
}
