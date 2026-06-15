<?php
namespace SchoolManagementApi\Controllers;

use WP_REST_Request;

class AnalyticsController extends BaseController {
    
    /**
     * GET /analytics
     */
    public function getAnalytics(WP_REST_Request $request) {
        global $wpdb;

        $table_students = $wpdb->prefix . 'school_students';
        $table_teachers = $wpdb->prefix . 'school_teachers';
        $table_attendance = $wpdb->prefix . 'school_attendance';
        $table_fees = $wpdb->prefix . 'school_fees';
        $table_marks = $wpdb->prefix . 'school_marks';

        // 1. Student Growth (New admissions by class)
        $student_growth = $wpdb->get_results("
            SELECT class_id, COUNT(*) as count 
            FROM $table_students 
            WHERE deleted_at IS NULL 
            GROUP BY class_id
        ", ARRAY_A) ?: [];

        // 2. Attendance Analytics (average present rate)
        $total_attendance = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_attendance WHERE deleted_at IS NULL");
        $present_attendance = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_attendance WHERE status = 'Present' AND deleted_at IS NULL");
        $attendance_rate = $total_attendance > 0 ? round(($present_attendance / $total_attendance) * 100, 2) : 100.00;

        // 3. Fee Analytics (Collection rate)
        $total_structured = (float)$wpdb->get_var("SELECT SUM(amount) FROM $table_fees WHERE type = 'STRUCTURE' AND deleted_at IS NULL");
        $total_collected = (float)$wpdb->get_var("SELECT SUM(amount) FROM $table_fees WHERE type = 'COLLECTION' AND deleted_at IS NULL");
        $collection_rate = $total_structured > 0 ? round(($total_collected / $total_structured) * 100, 2) : 100.00;

        // 4. Academic Performance (average grade percentage across exams)
        $average_score = (float)$wpdb->get_var("
            SELECT AVG(marks_obtained / max_marks * 100) 
            FROM $table_marks 
            WHERE deleted_at IS NULL
        ");
        $academic_performance = [
            'average_percentage' => round($average_score, 2),
            'grade' => $average_score >= 90 ? 'A+' : ($average_score >= 80 ? 'A' : ($average_score >= 70 ? 'B' : ($average_score >= 60 ? 'C' : 'D')))
        ];

        // 5. Teacher Performance (mock analytics mapping student metrics and count)
        $teachers_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_teachers WHERE status = 'ACTIVE' AND deleted_at IS NULL");
        $teacher_performance = [
            'total_active_teachers' => $teachers_count,
            'average_rating' => 4.6, // Mock metric representing parent/student feedbacks
            'retention_rate' => 98.2 // Mock percentage
        ];

        return $this->success('Analytics statements calculated successfully', [
            'student_growth' => $student_growth,
            'attendance_analytics' => [
                'total_records' => $total_attendance,
                'overall_present_percentage' => $attendance_rate
            ],
            'fee_analytics' => [
                'total_billed' => $total_structured,
                'total_collected' => $total_collected,
                'collection_percentage' => $collection_rate
            ],
            'academic_performance' => $academic_performance,
            'teacher_performance' => $teacher_performance
        ]);
    }
}
