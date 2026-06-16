<?php
namespace HospitalManagementApi\Controllers;

use WP_REST_Request;

class DashboardController extends BaseController {

    /**
     * GET /dashboard
     */
    public function getStats(WP_REST_Request $request) {
        global $wpdb;

        // 1. Basic Counts
        $total_patients = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hospital_patients WHERE deleted_at IS NULL");
        
        $today = current_time('Y-m-d');
        $today_appointments = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}hospital_appointments WHERE appointment_date = %s AND deleted_at IS NULL", $today));
        
        $doctors_available = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hospital_doctors WHERE status = 'ACTIVE' AND deleted_at IS NULL");
        
        $opd_patients = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hospital_opd WHERE deleted_at IS NULL");
        $ipd_patients = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hospital_ipd WHERE status = 'ADMITTED' AND deleted_at IS NULL");
        
        // 2. Financial Metrics
        $today_revenue = (float)$wpdb->get_var($wpdb->prepare("SELECT SUM(paid_amount) FROM {$wpdb->prefix}hospital_billing WHERE DATE(created_at) = %s AND deleted_at IS NULL", $today)) ?: 0.00;
        
        $pending_bills = (float)$wpdb->get_var("SELECT SUM(due_amount) FROM {$wpdb->prefix}hospital_billing WHERE status != 'PAID' AND deleted_at IS NULL") ?: 0.00;
        
        $pharmacy_sales = (float)$wpdb->get_var("SELECT SUM(medicine_charges) FROM {$wpdb->prefix}hospital_billing WHERE deleted_at IS NULL") ?: 0.00;

        // 3. Trends & Datasets
        // Revenue trends by month
        $revenue_trends = $wpdb->get_results(
            "SELECT DATE_FORMAT(created_at, '%b %Y') as label, SUM(paid_amount) as value 
             FROM {$wpdb->prefix}hospital_billing 
             WHERE deleted_at IS NULL 
             GROUP BY YEAR(created_at), MONTH(created_at) 
             ORDER BY YEAR(created_at) ASC, MONTH(created_at) ASC LIMIT 6",
            ARRAY_A
        );

        // Appointment status trends
        $appointment_trends = $wpdb->get_results(
            "SELECT status as label, COUNT(*) as value 
             FROM {$wpdb->prefix}hospital_appointments 
             WHERE deleted_at IS NULL 
             GROUP BY status",
            ARRAY_A
        );

        // Patient growth trends
        $patient_growth = $wpdb->get_results(
            "SELECT DATE_FORMAT(created_at, '%b %Y') as label, COUNT(*) as value 
             FROM {$wpdb->prefix}hospital_patients 
             WHERE deleted_at IS NULL 
             GROUP BY YEAR(created_at), MONTH(created_at) 
             ORDER BY YEAR(created_at) ASC, MONTH(created_at) ASC LIMIT 6",
            ARRAY_A
        );

        // Doctor performance trends (Appointments count)
        $doctor_performance = $wpdb->get_results(
            "SELECT d.name as label, COUNT(a.id) as value 
             FROM {$wpdb->prefix}hospital_doctors d 
             LEFT JOIN {$wpdb->prefix}hospital_appointments a ON d.id = a.doctor_id AND a.deleted_at IS NULL 
             WHERE d.deleted_at IS NULL 
             GROUP BY d.id",
            ARRAY_A
        );

        return $this->success('Dashboard metrics retrieved successfully.', [
            'cards' => [
                'total_patients' => $total_patients,
                'today_appointments' => $today_appointments,
                'doctors_available' => $doctors_available,
                'opd_patients' => $opd_patients,
                'ipd_patients' => $ipd_patients,
                'today_revenue' => $today_revenue,
                'pending_bills' => $pending_bills,
                'pharmacy_sales' => $pharmacy_sales
            ],
            'charts' => [
                'revenue_trends' => $revenue_trends ?: [],
                'appointment_trends' => $appointment_trends ?: [],
                'patient_growth' => $patient_growth ?: [],
                'doctor_performance' => $doctor_performance ?: []
            ]
        ]);
    }
}
