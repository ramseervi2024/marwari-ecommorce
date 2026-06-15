<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\ExamRepository;
use SchoolManagementApi\Repositories\MarkRepository;
use SchoolManagementApi\Repositories\StudentRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class ExamController extends BaseController {
    private $examRepo;
    private $markRepo;
    private $studentRepo;

    public function __construct() {
        $this->examRepo = new ExamRepository();
        $this->markRepo = new MarkRepository();
        $this->studentRepo = new StudentRepository();
    }

    // --- Exams CRUD ---

    public function getExams(WP_REST_Request $request) {
        $params = $request->get_params();
        $result = $this->examRepo->findAll($params, ['id', 'exam_name', 'exam_type', 'start_date', 'status']);
        return $this->success('Exams fetched successfully', $result);
    }

    public function createExam(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['exam_name'])) {
            return $this->error('Validation failed: exam_name is required.');
        }

        $data = [
            'exam_name' => sanitize_text_field($params['exam_name']),
            'exam_type' => isset($params['exam_type']) ? sanitize_text_field($params['exam_type']) : null,
            'start_date' => isset($params['start_date']) ? sanitize_text_field($params['start_date']) : null,
            'end_date' => isset($params['end_date']) ? sanitize_text_field($params['end_date']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $exam_id = $this->examRepo->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        if (!$exam_id) {
            return $this->error('Failed to create exam.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_EXAM', "Created exam {$params['exam_name']} (ID: $exam_id)");
        return $this->success('Exam created successfully', array_merge(['id' => $exam_id], $data), 201);
    }

    public function updateExam(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        if (!$this->examRepo->findById($id)) {
            return $this->error('Exam not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'exam_name' => '%s',
            'exam_type' => '%s',
            'start_date' => '%s',
            'end_date' => '%s',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                $data[$field] = sanitize_text_field($params[$field]);
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->examRepo->update($id, $data, $formats);
        return $this->success('Exam updated successfully', $this->examRepo->findById($id));
    }

    public function deleteExam(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        if (!$this->examRepo->findById($id)) {
            return $this->error('Exam not found.', [], 404);
        }

        $this->examRepo->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DELETE_EXAM', "Deleted exam ID: $id");
        return $this->success('Exam deleted successfully');
    }

    // --- Marks Entry ---

    public function enterMarks(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['exam_id']) || empty($params['student_id']) || empty($params['subject_id']) || !isset($params['marks_obtained'])) {
            return $this->error('Validation failed: exam_id, student_id, subject_id, and marks_obtained are required.');
        }

        $data = [
            'exam_id' => (int)$params['exam_id'],
            'student_id' => (int)$params['student_id'],
            'subject_id' => (int)$params['subject_id'],
            'marks_obtained' => (float)$params['marks_obtained'],
            'max_marks' => isset($params['max_marks']) ? (float)$params['max_marks'] : 100.00,
            'remarks' => isset($params['remarks']) ? sanitize_text_field($params['remarks']) : null,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $mark_id = $this->markRepo->create($data, ['%d', '%d', '%d', '%f', '%f', '%s', '%s', '%s']);
        if (!$mark_id) {
            return $this->error('Failed to register marks.');
        }

        AuthService::logActivity(get_current_user_id(), 'ENTER_MARKS', "Recorded marks for student ID: {$params['student_id']} in exam ID: {$params['exam_id']}");
        return $this->success('Marks registered successfully', array_merge(['id' => $mark_id], $data), 201);
    }

    public function updateMarks(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        if (!$this->markRepo->findById($id)) {
            return $this->error('Marks entry not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['marks_obtained'])) {
            $data['marks_obtained'] = (float)$params['marks_obtained'];
            $formats[] = '%f';
        }
        if (isset($params['max_marks'])) {
            $data['max_marks'] = (float)$params['max_marks'];
            $formats[] = '%f';
        }
        if (isset($params['remarks'])) {
            $data['remarks'] = sanitize_text_field($params['remarks']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->markRepo->update($id, $data, $formats);
        return $this->success('Marks entry updated successfully', $this->markRepo->findById($id));
    }

    public function getStudentMarks(WP_REST_Request $request) {
        $student_id = (int)$request->get_param('studentId');
        
        $params = $request->get_params();
        $filters = ['student_id' => $student_id];
        
        $result = $this->markRepo->findAll($params, ['id', 'exam_id', 'subject_id'], [], $filters);
        return $this->success('Student marks fetched successfully', $result);
    }

    // --- Report Cards ---

    public function getReportCards(WP_REST_Request $request) {
        $params = $request->get_params();
        $result = $this->studentRepo->findAll($params, ['id', 'admission_no', 'first_name', 'last_name']);
        
        foreach ($result['data'] as &$student) {
            $student['grades_summary'] = $this->calculateStudentGrades($student['id']);
        }

        return $this->success('Report cards indices fetched successfully', $result);
    }

    public function getStudentReportCard(WP_REST_Request $request) {
        $student_id = (int)$request->get_param('studentId');
        $student = $this->studentRepo->findById($student_id);

        if (!$student) {
            return $this->error('Student not found.', [], 404);
        }

        $grades = $this->calculateStudentGrades($student_id);

        // Build a beautiful mock HTML printable base64 report card
        $html = "
        <div style='font-family: Arial, sans-serif; padding: 30px; border: 2px solid #333; max-width: 600px; margin: auto;'>
            <h2 style='text-align: center; color: #4F46E5;'>ACADEMIC REPORT CARD</h2>
            <h4 style='text-align: center; color: #555;'>Global International School</h4>
            <hr>
            <p><strong>Name:</strong> {$student['first_name']} {$student['last_name']}</p>
            <p><strong>Admission No:</strong> {$student['admission_no']}</p>
            <p><strong>Roll No:</strong> {$student['roll_no']}</p>
            <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
                <thead>
                    <tr style='background: #F3F4F6;'>
                        <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Subject ID</th>
                        <th style='border: 1px solid #ddd; padding: 8px; text-align: right;'>Obtained</th>
                        <th style='border: 1px solid #ddd; padding: 8px; text-align: right;'>Max Marks</th>
                        <th style='border: 1px solid #ddd; padding: 8px; text-align: center;'>Percentage</th>
                    </tr>
                </thead>
                <tbody>";
        
        foreach ($grades['marks_breakdown'] as $mark) {
            $perc = round(($mark['marks_obtained'] / $mark['max_marks']) * 100, 2);
            $html .= "
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 8px;'>Subject #{$mark['subject_id']} (Exam #{$mark['exam_id']})</td>
                        <td style='border: 1px solid #ddd; padding: 8px; text-align: right;'>{$mark['marks_obtained']}</td>
                        <td style='border: 1px solid #ddd; padding: 8px; text-align: right;'>{$mark['max_marks']}</td>
                        <td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>{$perc}%</td>
                    </tr>";
        }

        $html .= "
                </tbody>
            </table>
            <div style='margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px;'>
                <p><strong>Average Grade:</strong> {$grades['gpa_average']}%</p>
                <p><strong>Academic Standing:</strong> " . ($grades['gpa_average'] >= 50 ? 'Passed' : 'Needs Improvement') . "</p>
            </div>
        </div>";

        return $this->success('Student report card generated successfully', [
            'student' => $student,
            'summary' => $grades,
            'pdf_printable_html' => base64_encode($html)
        ]);
    }

    private function calculateStudentGrades(int $student_id): array {
        global $wpdb;
        $table_marks = $wpdb->prefix . 'school_marks';
        
        $marks = $wpdb->get_results($wpdb->prepare("
            SELECT exam_id, subject_id, marks_obtained, max_marks
            FROM $table_marks
            WHERE student_id = %d AND deleted_at IS NULL
        ", $student_id), ARRAY_A) ?: [];

        $total_obtained = 0;
        $total_max = 0;
        foreach ($marks as $mark) {
            $total_obtained += (float)$mark['marks_obtained'];
            $total_max += (float)$mark['max_marks'];
        }

        $avg = $total_max > 0 ? round(($total_obtained / $total_max) * 100, 2) : 0.00;

        return [
            'marks_breakdown' => $marks,
            'total_obtained' => $total_obtained,
            'total_max' => $total_max,
            'gpa_average' => $avg
        ];
    }
}
