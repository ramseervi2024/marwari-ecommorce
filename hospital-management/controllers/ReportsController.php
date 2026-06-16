<?php
namespace HospitalManagementApi\Controllers;

use WP_REST_Request;

class ReportsController extends BaseController {

    public function getRevenueReport(WP_REST_Request $request) {
        global $wpdb;
        $data = $wpdb->get_results(
            "SELECT DATE(created_at) as date, SUM(total_amount) as total_billed, SUM(paid_amount) as total_received 
             FROM {$wpdb->prefix}hospital_billing 
             WHERE deleted_at IS NULL 
             GROUP BY DATE(created_at) ORDER BY DATE(created_at) DESC LIMIT 30",
            ARRAY_A
        );
        return $this->success('Revenue reports retrieved.', $data ?: []);
    }

    public function getPatientsReport(WP_REST_Request $request) {
        global $wpdb;
        $data = $wpdb->get_results(
            "SELECT gender, COUNT(*) as count FROM {$wpdb->prefix}hospital_patients WHERE deleted_at IS NULL GROUP BY gender",
            ARRAY_A
        );
        return $this->success('Patients demographics reports retrieved.', $data ?: []);
    }

    public function getDoctorsReport(WP_REST_Request $request) {
        global $wpdb;
        $data = $wpdb->get_results(
            "SELECT specialization, COUNT(*) as count FROM {$wpdb->prefix}hospital_doctors WHERE deleted_at IS NULL GROUP BY specialization",
            ARRAY_A
        );
        return $this->success('Doctors specialty reports retrieved.', $data ?: []);
    }

    public function getPharmacyReport(WP_REST_Request $request) {
        global $wpdb;
        $data = $wpdb->get_results(
            "SELECT medicine_name, quantity, selling_price, expiry_date 
             FROM {$wpdb->prefix}hospital_pharmacy 
             WHERE deleted_at IS NULL ORDER BY quantity ASC",
            ARRAY_A
        );
        return $this->success('Pharmacy inventory level reports retrieved.', $data ?: []);
    }

    public function getLaboratoryReport(WP_REST_Request $request) {
        global $wpdb;
        $data = $wpdb->get_results(
            "SELECT t.test_name, COUNT(r.id) as tests_conducted 
             FROM {$wpdb->prefix}hospital_lab_tests t 
             LEFT JOIN {$wpdb->prefix}hospital_lab_reports r ON t.id = r.test_id AND r.deleted_at IS NULL 
             WHERE t.deleted_at IS NULL 
             GROUP BY t.id",
            ARRAY_A
        );
        return $this->success('Laboratory tests execution reports retrieved.', $data ?: []);
    }

    public function getAppointmentsReport(WP_REST_Request $request) {
        global $wpdb;
        $data = $wpdb->get_results(
            "SELECT appointment_type, COUNT(*) as count FROM {$wpdb->prefix}hospital_appointments WHERE deleted_at IS NULL GROUP BY appointment_type",
            ARRAY_A
        );
        return $this->success('Appointments reports retrieved.', $data ?: []);
    }

    public function getBillingReport(WP_REST_Request $request) {
        global $wpdb;
        $data = $wpdb->get_results(
            "SELECT status, COUNT(*) as count, SUM(total_amount) as total_amount FROM {$wpdb->prefix}hospital_billing WHERE deleted_at IS NULL GROUP BY status",
            ARRAY_A
        );
        return $this->success('Billing status reports retrieved.', $data ?: []);
    }

    public function getOpdReport(WP_REST_Request $request) {
        global $wpdb;
        $data = $wpdb->get_results(
            "SELECT visit_date, COUNT(*) as total_visits, SUM(consultation_fee) as revenue 
             FROM {$wpdb->prefix}hospital_opd 
             WHERE deleted_at IS NULL 
             GROUP BY visit_date ORDER BY visit_date DESC LIMIT 30",
            ARRAY_A
        );
        return $this->success('OPD clinical reports retrieved.', $data ?: []);
    }

    public function getIpdReport(WP_REST_Request $request) {
        global $wpdb;
        $data = $wpdb->get_results(
            "SELECT ward, COUNT(*) as admitted_patients FROM {$wpdb->prefix}hospital_ipd WHERE status = 'ADMITTED' AND deleted_at IS NULL GROUP BY ward",
            ARRAY_A
        );
        return $this->success('IPD ward bed occupancy reports retrieved.', $data ?: []);
    }
}
