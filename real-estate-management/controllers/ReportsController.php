<?php
namespace RealEstateManagementApi\Controllers;

use WP_REST_Request;

class ReportsController extends BaseController {

    /**
     * GET /reports/leads
     */
    public function getLeadsReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'realestate_leads';

        $status_summary = $wpdb->get_results("SELECT lead_status, COUNT(*) as count FROM $table WHERE deleted_at IS NULL GROUP BY lead_status", ARRAY_A);
        $source_summary = $wpdb->get_results("SELECT source, COUNT(*) as count FROM $table WHERE deleted_at IS NULL GROUP BY source", ARRAY_A);

        return $this->success('Leads report retrieved.', [
            'by_status' => $status_summary ?: [],
            'by_source' => $source_summary ?: []
        ]);
    }

    /**
     * GET /reports/site-visits
     */
    public function getSiteVisitsReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'realestate_site_visits';

        $status_summary = $wpdb->get_results("SELECT status, COUNT(*) as count FROM $table WHERE deleted_at IS NULL GROUP BY status", ARRAY_A);
        $monthly_summary = $wpdb->get_results("SELECT DATE_FORMAT(visit_date, '%b %Y') as month, COUNT(*) as count FROM $table WHERE deleted_at IS NULL GROUP BY DATE_FORMAT(visit_date, '%Y-%m')", ARRAY_A);

        return $this->success('Site visits report retrieved.', [
            'by_status' => $status_summary ?: [],
            'by_month' => $monthly_summary ?: []
        ]);
    }

    /**
     * GET /reports/bookings
     */
    public function getBookingsReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'realestate_bookings';

        $status_summary = $wpdb->get_results("SELECT status, COUNT(*) as count, SUM(final_price) as total_value FROM $table WHERE deleted_at IS NULL GROUP BY status", ARRAY_A);
        
        return $this->success('Bookings report retrieved.', [
            'by_status' => $status_summary ?: []
        ]);
    }

    /**
     * GET /reports/payments
     */
    public function getPaymentsReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'realestate_payment_schedules';

        $status_summary = $wpdb->get_results("SELECT payment_status, COUNT(*) as count, SUM(amount) as total_amount FROM $table WHERE deleted_at IS NULL GROUP BY payment_status", ARRAY_A);
        
        return $this->success('Payments report retrieved.', [
            'by_status' => $status_summary ?: []
        ]);
    }

    /**
     * GET /reports/collections
     */
    public function getCollectionsReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'realestate_payment_schedules';

        $monthly_collections = $wpdb->get_results("SELECT DATE_FORMAT(due_date, '%b %Y') as month, SUM(paid_amount) as collected FROM $table WHERE paid_amount > 0 AND deleted_at IS NULL GROUP BY DATE_FORMAT(due_date, '%Y-%m') ORDER BY due_date ASC", ARRAY_A);

        return $this->success('Collections report retrieved.', [
            'monthly' => $monthly_collections ?: []
        ]);
    }

    /**
     * GET /reports/commissions
     */
    public function getCommissionsReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'realestate_commissions';

        $status_summary = $wpdb->get_results("SELECT payment_status, COUNT(*) as count, SUM(commission_amount) as total_amount, SUM(paid_amount) as total_paid FROM $table WHERE deleted_at IS NULL GROUP BY payment_status", ARRAY_A);

        return $this->success('Commissions report retrieved.', [
            'by_status' => $status_summary ?: []
        ]);
    }

    /**
     * GET /reports/projects
     */
    public function getProjectsReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'realestate_projects';

        $project_summary = $wpdb->get_results("SELECT status, COUNT(*) as count FROM $table WHERE deleted_at IS NULL GROUP BY status", ARRAY_A);

        return $this->success('Projects report retrieved.', [
            'by_status' => $project_summary ?: []
        ]);
    }

    /**
     * GET /reports/sales
     */
    public function getSalesReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'realestate_bookings';

        $sales_summary = $wpdb->get_results("SELECT DATE_FORMAT(booking_date, '%b %Y') as month, SUM(final_price) as sales_value FROM $table WHERE status = 'Confirmed' AND deleted_at IS NULL GROUP BY DATE_FORMAT(booking_date, '%Y-%m') ORDER BY booking_date ASC", ARRAY_A);

        return $this->success('Sales performance report retrieved.', [
            'monthly' => $sales_summary ?: []
        ]);
    }

    /**
     * GET /reports/profit-loss
     */
    public function getProfitLossReport(WP_REST_Request $request) {
        global $wpdb;
        $table_payments = $wpdb->prefix . 'realestate_payment_schedules';
        $table_commissions = $wpdb->prefix . 'realestate_commissions';

        $total_revenue = (float)$wpdb->get_var("SELECT SUM(paid_amount) FROM $table_payments WHERE deleted_at IS NULL");
        $broker_outflow = (float)$wpdb->get_var("SELECT SUM(paid_amount) FROM $table_commissions WHERE deleted_at IS NULL");
        
        $net_revenue = $total_revenue - $broker_outflow;

        return $this->success('Profit and Loss financial report retrieved.', [
            'total_revenue' => $total_revenue,
            'broker_commission_outflow' => $broker_outflow,
            'net_operating_revenue' => $net_revenue
        ]);
    }
}
