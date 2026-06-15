<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\ClassRepository;
use SchoolManagementApi\Repositories\SectionRepository;
use SchoolManagementApi\Repositories\SubjectRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class AcademicController extends BaseController {
    private $classRepo;
    private $sectionRepo;
    private $subjectRepo;

    public function __construct() {
        $this->classRepo = new ClassRepository();
        $this->sectionRepo = new SectionRepository();
        $this->subjectRepo = new SubjectRepository();
    }

    // --- Classes CRUD ---

    public function getClasses(WP_REST_Request $request) {
        $params = $request->get_params();
        $result = $this->classRepo->findAll($params, ['id', 'class_name', 'status'], ['class_name']);
        return $this->success('Classes fetched successfully', $result);
    }

    public function createClass(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['class_name'])) {
            return $this->error('Validation failed: class_name is required.');
        }

        $data = [
            'class_name' => sanitize_text_field($params['class_name']),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $class_id = $this->classRepo->create($data, ['%s', '%s', '%s', '%s']);
        if (!$class_id) {
            return $this->error('Failed to create class.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_CLASS', "Created class {$params['class_name']} (ID: $class_id)");
        return $this->success('Class created successfully', array_merge(['id' => $class_id], $data), 201);
    }

    public function updateClass(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        if (!$this->classRepo->findById($id)) {
            return $this->error('Class not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['class_name'])) {
            $data['class_name'] = sanitize_text_field($params['class_name']);
            $formats[] = '%s';
        }
        if (isset($params['status'])) {
            $data['status'] = sanitize_text_field($params['status']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->classRepo->update($id, $data, $formats);
        return $this->success('Class updated successfully', $this->classRepo->findById($id));
    }

    public function deleteClass(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        if (!$this->classRepo->findById($id)) {
            return $this->error('Class not found.', [], 404);
        }

        $this->classRepo->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DELETE_CLASS', "Deleted class ID: $id");
        return $this->success('Class deleted successfully');
    }

    // --- Sections CRUD ---

    public function getSections(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = [];
        if (!empty($params['class_id'])) {
            $filters['class_id'] = (int)$params['class_id'];
        }
        $result = $this->sectionRepo->findAll($params, ['id', 'section_name', 'class_id'], ['section_name'], $filters);
        return $this->success('Sections fetched successfully', $result);
    }

    public function createSection(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['section_name']) || empty($params['class_id'])) {
            return $this->error('Validation failed: section_name and class_id are required.');
        }

        $data = [
            'section_name' => sanitize_text_field($params['section_name']),
            'class_id' => (int)$params['class_id'],
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $sec_id = $this->sectionRepo->create($data, ['%s', '%d', '%s', '%s', '%s']);
        if (!$sec_id) {
            return $this->error('Failed to create section.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_SECTION', "Created section {$params['section_name']} for class ID: {$params['class_id']}");
        return $this->success('Section created successfully', array_merge(['id' => $sec_id], $data), 201);
    }

    public function updateSection(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        if (!$this->sectionRepo->findById($id)) {
            return $this->error('Section not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['section_name'])) {
            $data['section_name'] = sanitize_text_field($params['section_name']);
            $formats[] = '%s';
        }
        if (isset($params['class_id'])) {
            $data['class_id'] = (int)$params['class_id'];
            $formats[] = '%d';
        }
        if (isset($params['status'])) {
            $data['status'] = sanitize_text_field($params['status']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->sectionRepo->update($id, $data, $formats);
        return $this->success('Section updated successfully', $this->sectionRepo->findById($id));
    }

    public function deleteSection(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        if (!$this->sectionRepo->findById($id)) {
            return $this->error('Section not found.', [], 404);
        }

        $this->sectionRepo->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DELETE_SECTION', "Deleted section ID: $id");
        return $this->success('Section deleted successfully');
    }

    // --- Subjects CRUD ---

    public function getSubjects(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = [];
        if (!empty($params['class_id'])) {
            $filters['class_id'] = (int)$params['class_id'];
        }
        if (!empty($params['teacher_id'])) {
            $filters['teacher_id'] = (int)$params['teacher_id'];
        }
        $result = $this->subjectRepo->findAll($params, ['id', 'subject_name', 'subject_code', 'class_id', 'teacher_id'], ['subject_name', 'subject_code'], $filters);
        return $this->success('Subjects fetched successfully', $result);
    }

    public function createSubject(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['subject_name']) || empty($params['class_id'])) {
            return $this->error('Validation failed: subject_name and class_id are required.');
        }

        $data = [
            'subject_name' => sanitize_text_field($params['subject_name']),
            'subject_code' => isset($params['subject_code']) ? sanitize_text_field($params['subject_code']) : null,
            'class_id' => (int)$params['class_id'],
            'teacher_id' => isset($params['teacher_id']) ? (int)$params['teacher_id'] : null,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $sub_id = $this->subjectRepo->create($data, ['%s', '%s', '%d', '%d', '%s', '%s', '%s']);
        if (!$sub_id) {
            return $this->error('Failed to create subject.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_SUBJECT', "Created subject {$params['subject_name']} for class ID: {$params['class_id']}");
        return $this->success('Subject created successfully', array_merge(['id' => $sub_id], $data), 201);
    }

    public function updateSubject(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        if (!$this->subjectRepo->findById($id)) {
            return $this->error('Subject not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'subject_name' => '%s',
            'subject_code' => '%s',
            'class_id' => '%d',
            'teacher_id' => '%d',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                $data[$field] = $format === '%d' ? (int)$params[$field] : sanitize_text_field($params[$field]);
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->subjectRepo->update($id, $data, $formats);
        return $this->success('Subject updated successfully', $this->subjectRepo->findById($id));
    }

    public function deleteSubject(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        if (!$this->subjectRepo->findById($id)) {
            return $this->error('Subject not found.', [], 404);
        }

        $this->subjectRepo->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DELETE_SUBJECT', "Deleted subject ID: $id");
        return $this->success('Subject deleted successfully');
    }
}
