<?php
namespace RealEstateManagementApi\Controllers;

use WP_REST_Request;

class DashboardController extends BaseController {
    
    /**
     * GET /dashboard
     */
    public function getDashboardData(WP_REST_Request $request) {
        global $wpdb;
        
        $table_leads = $wpdb->prefix . 'realestate_leads';
        $table_properties = $wpdb->prefix . 'realestate_properties';
        $table_site_visits = $wpdb->prefix . 'realestate_site_visits';
        $table_bookings = $wpdb->prefix . 'realestate_bookings';
        $table_payments = $wpdb->prefix . 'realestate_payment_schedules';
        $table_commissions = $wpdb->prefix . 'realestate_commissions';
        $table_brokers = $wpdb->prefix . 'realestate_brokers';
        
        $today = current_time('Y-m-d');
        $start_of_month = date('Y-m-01');
        $end_of_month = date('Y-m-t');

        // 1. KPI Cards
        $new_leads = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_leads WHERE lead_status = 'New' AND deleted_at IS NULL");
        $active_leads = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_leads WHERE lead_status NOT IN ('Booked', 'Lost') AND deleted_at IS NULL");
        $site_visits_today = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_site_visits WHERE visit_date = %s AND deleted_at IS NULL", $today));
        $properties_available = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_properties WHERE status = 'Available' AND deleted_at IS NULL");
        $bookings_this_month = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_bookings WHERE booking_date BETWEEN %s AND %s AND status = 'Confirmed' AND deleted_at IS NULL", $start_of_month, $end_of_month));
        
        $collection_amount = (float)$wpdb->get_var("SELECT SUM(paid_amount) FROM $table_payments WHERE deleted_at IS NULL");
        $pending_payments = (float)$wpdb->get_var("SELECT SUM(balance_amount) FROM $table_payments WHERE payment_status != 'Paid' AND deleted_at IS NULL");
        $broker_commissions = (float)$wpdb->get_var("SELECT SUM(commission_amount) FROM $table_commissions WHERE deleted_at IS NULL");

        // 2. Analytics calculations
        // Lead Conversion Rate
        $total_leads = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_leads WHERE deleted_at IS NULL");
        $booked_leads = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_leads WHERE lead_status = 'Booked' AND deleted_at IS NULL");
        $conversion_rate = $total_leads > 0 ? round(($booked_leads / $total_leads) * 100, 2) : 0;

        // Sales Performance (Bookings count monthly)
        $sales_performance = $wpdb->get_results("
            SELECT DATE_FORMAT(booking_date, '%b %Y') as month, COUNT(*) as count, SUM(final_price) as value 
            FROM $table_bookings 
            WHERE status = 'Confirmed' AND deleted_at IS NULL 
            GROUP BY DATE_FORMAT(booking_date, '%Y-%m') 
            ORDER BY booking_date ASC 
            LIMIT 6
        ", ARRAY_A);

        // Revenue Trends (Collections monthly)
        $revenue_trends = $wpdb->get_results("
            SELECT DATE_FORMAT(due_date, '%b %Y') as month, SUM(paid_amount) as collected 
            FROM $table_payments 
            WHERE paid_amount > 0 AND deleted_at IS NULL 
            GROUP BY DATE_FORMAT(due_date, '%Y-%m') 
            ORDER BY due_date ASC 
            LIMIT 6
        ", ARRAY_A);

        // Project-wise Sales
        $project_sales = $wpdb->get_results("
            SELECT p.project_name, COUNT(b.id) as bookings_count, SUM(b.final_price) as revenue 
            FROM $table_bookings b 
            JOIN $table_properties pr ON b.property_id = pr.id 
            JOIN " . $wpdb->prefix . "realestate_projects p ON pr.project_id = p.id 
            WHERE b.status = 'Confirmed' AND b.deleted_at IS NULL AND p.deleted_at IS NULL
            GROUP BY p.id
        ", ARRAY_A);

        // Broker Performance
        $broker_performance = $wpdb->get_results("
            SELECT br.broker_name, COUNT(c.id) as bookings_referred, SUM(c.commission_amount) as total_earned 
            FROM $table_commissions c 
            JOIN $table_brokers br ON c.broker_id = br.id 
            WHERE c.deleted_at IS NULL AND br.deleted_at IS NULL 
            GROUP BY br.id 
            ORDER BY total_earned DESC 
            LIMIT 5
        ", ARRAY_A);

        return $this->success('Dashboard metrics loaded successfully.', [
            'cards' => [
                'new_leads' => $new_leads,
                'active_leads' => $active_leads,
                'site_visits_today' => $site_visits_today,
                'properties_available' => $properties_available,
                'bookings_this_month' => $bookings_this_month,
                'collection_amount' => $collection_amount,
                'pending_payments' => $pending_payments,
                'broker_commissions' => $broker_commissions
            ],
            'analytics' => [
                'conversion_rate' => $conversion_rate,
                'sales_performance' => $sales_performance ?: [],
                'revenue_trends' => $revenue_trends ?: [],
                'project_sales' => $project_sales ?: [],
                'broker_performance' => $broker_performance ?: []
            ]
        ]);
    }
}
