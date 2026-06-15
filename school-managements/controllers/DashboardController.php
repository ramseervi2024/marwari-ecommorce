<?php
namespace SchoolManagementApi\Controllers;

use WP_REST_Request;

class DashboardController extends BaseController {
    
    /**
     * GET /dashboard
     */
    public function getSummary(WP_REST_Request $request) {
        global $wpdb;
        
        $table_students = $wpdb->prefix . 'school_students';
        $table_teachers = $wpdb->prefix . 'school_teachers';
        $table_parents = $wpdb->prefix . 'school_parents';
        $table_classes = $wpdb->prefix . 'school_classes';
        $table_attendance = $wpdb->prefix . 'school_attendance';
        $table_fees = $wpdb->prefix . 'school_fees';
        $table_exams = $wpdb->prefix . 'school_exams';

        // 1. Dashboard Cards
        $total_students = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_students WHERE deleted_at IS NULL");
        $total_teachers = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_teachers WHERE deleted_at IS NULL");
        $total_parents = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_parents WHERE deleted_at IS NULL");
        $active_classes = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_classes WHERE status = 'ACTIVE' AND deleted_at IS NULL");
        
        $today = current_time('Y-m-d');
        $today_attendance = $wpdb->get_results($wpdb->prepare("
            SELECT status, COUNT(*) as count 
            FROM $table_attendance 
            WHERE attendance_date = %s AND deleted_at IS NULL
            GROUP BY status
        ", $today), ARRAY_A) ?: [];

        $attendance_summary = [
            'Present' => 0,
            'Absent' => 0,
            'Late' => 0,
            'Half Day' => 0
        ];
        foreach ($today_attendance as $row) {
            if (isset($attendance_summary[$row['status']])) {
                $attendance_summary[$row['status']] = (int)$row['count'];
            }
        }

        $current_month = current_time('Y-m');
        $monthly_fees_collected = (float)$wpdb->get_var($wpdb->prepare("
            SELECT SUM(amount) 
            FROM $table_fees 
            WHERE type = 'COLLECTION' AND DATE_FORMAT(paid_at, '%Y-%m') = %s AND deleted_at IS NULL
        ", $current_month));

        // Pending fees count
        $pending_fees_total = (float)$wpdb->get_var("
            SELECT SUM(amount) 
            FROM $table_fees 
            WHERE type = 'STRUCTURE' AND deleted_at IS NULL
        ") - (float)$wpdb->get_var("
            SELECT SUM(amount) 
            FROM $table_fees 
            WHERE type = 'COLLECTION' AND deleted_at IS NULL
        ");
        if ($pending_fees_total < 0) {
            $pending_fees_total = 0.00;
        }

        $upcoming_exams_count = (int)$wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) 
            FROM $table_exams 
            WHERE start_date >= %s AND deleted_at IS NULL
        ", $today));

        // 2. Admission Trends (Simulated or Real from table)
        $admission_trends = $wpdb->get_results("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
            FROM $table_students
            WHERE deleted_at IS NULL
            GROUP BY month
            ORDER BY month ASC
            LIMIT 6
        ", ARRAY_A) ?: [];

        // 3. Fee Collection Trends
        $fee_collection_trends = $wpdb->get_results("
            SELECT DATE_FORMAT(paid_at, '%Y-%m') as month, SUM(amount) as collected
            FROM $table_fees
            WHERE type = 'COLLECTION' AND deleted_at IS NULL
            GROUP BY month
            ORDER BY month ASC
            LIMIT 6
        ", ARRAY_A) ?: [];

        // 4. Attendance Trends (Last 7 Days)
        $attendance_trends = $wpdb->get_results("
            SELECT attendance_date as date, status, COUNT(*) as count
            FROM $table_attendance
            WHERE deleted_at IS NULL
            GROUP BY date, status
            ORDER BY date DESC
            LIMIT 28
        ", ARRAY_A) ?: [];

        // 5. Exam Performance Trends
        $table_marks = $wpdb->prefix . 'school_marks';
        $exam_performance = $wpdb->get_results("
            SELECT e.exam_name, AVG(m.marks_obtained / m.max_marks * 100) as average_percentage
            FROM $table_marks m
            JOIN $table_exams e ON m.exam_id = e.id
            WHERE m.deleted_at IS NULL AND e.deleted_at IS NULL
            GROUP BY e.id
            LIMIT 5
        ", ARRAY_A) ?: [];

        return $this->success('Dashboard metrics fetched successfully', [
            'cards' => [
                'total_students' => $total_students,
                'total_teachers' => $total_teachers,
                'total_parents' => $total_parents,
                'active_classes' => $active_classes,
                'today_attendance' => $attendance_summary,
                'monthly_fee_collection' => $monthly_fees_collected,
                'pending_fees' => $pending_fees_total,
                'upcoming_exams' => $upcoming_exams_count
            ],
            'charts' => [
                'admission_trends' => $admission_trends,
                'fee_trends' => $fee_collection_trends,
                'attendance_trends' => $attendance_trends,
                'exam_performance' => $exam_performance
            ]
        ]);
    }
}
