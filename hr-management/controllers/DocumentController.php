<?php
namespace HrManagementApi\Controllers;

use HrManagementApi\Repositories\EmployeeRepository;
use HrManagementApi\Repositories\DocumentRepository;
use HrManagementApi\Services\AuthService;
use WP_REST_Request;

class DocumentController extends BaseController {
    private $employeeRepository;
    private $documentRepository;

    public function __construct() {
        $this->employeeRepository = new EmployeeRepository();
        $this->documentRepository = new DocumentRepository();
    }

    /**
     * GET /documents
     * Admin/Manager sees all; Employee sees own
     */
    public function getAll(WP_REST_Request $request) {
        $params       = $request->get_params();
        $current_user = wp_get_current_user();
        $allowed_sorts = ['id', 'employee_id', 'document_type', 'status', 'uploaded_at'];
        $extra_filters = [];

        if (!$current_user->has_cap('manage_documents')) {
            $emp = $this->employeeRepository->findByUserId($current_user->ID);
            if (!$emp) {
                return $this->error('No employee profile linked to your account.', [], 404);
            }
            $extra_filters['employee_id'] = $emp['id'];
        } elseif (isset($params['employee_id'])) {
            $extra_filters['employee_id'] = intval($params['employee_id']);
        }

        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['document_type'])) {
            $extra_filters['document_type'] = sanitize_text_field($params['document_type']);
        }

        $results = $this->documentRepository->findAll($params, $allowed_sorts, ['document_name', 'document_type'], $extra_filters);

        foreach ($results['data'] as &$row) {
            $emp  = $this->employeeRepository->findById($row['employee_id']);
            $user = $emp ? get_userdata($emp['user_id']) : null;
            $row['employee_name'] = $user ? $user->display_name : 'Unknown';
        }

        return $this->success('Documents retrieved.', $results);
    }

    /**
     * GET /documents/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $doc = $this->documentRepository->findById($id);
        if (!$doc) {
            return $this->error('Document not found.', [], 404);
        }

        // Employees only view their own
        $current_user = wp_get_current_user();
        if (!$current_user->has_cap('manage_documents')) {
            $emp = $this->employeeRepository->findByUserId($current_user->ID);
            if (!$emp || $emp['id'] !== (int)$doc['employee_id']) {
                return $this->error('Access denied.', [], 403);
            }
        }

        $emp  = $this->employeeRepository->findById($doc['employee_id']);
        $user = $emp ? get_userdata($emp['user_id']) : null;
        $doc['employee_name'] = $user ? $user->display_name : 'Unknown';

        return $this->success('Document retrieved.', $doc);
    }

    /**
     * POST /documents
     * Upload/register a document (both Admin and Employee)
     */
    public function create(WP_REST_Request $request) {
        $params       = $request->get_json_params();
        $current_user = wp_get_current_user();

        // Determine employee_id
        if ($current_user->has_cap('manage_documents') && isset($params['employee_id'])) {
            $employee_id = intval($params['employee_id']);
        } else {
            // Employees register only for themselves
            $emp = $this->employeeRepository->findByUserId($current_user->ID);
            if (!$emp) {
                return $this->error('No employee profile linked to your account.', [], 404);
            }
            $employee_id = $emp['id'];
        }

        $document_name = sanitize_text_field($params['document_name'] ?? '');
        $document_type = sanitize_text_field($params['document_type'] ?? 'ID Proof');
        $file_url      = esc_url_raw($params['file_url'] ?? '');

        if (empty($document_name)) {
            return $this->error('document_name is required.');
        }

        $emp = $this->employeeRepository->findById($employee_id);
        if (!$emp) {
            return $this->error('Employee not found.', [], 404);
        }

        $id = $this->documentRepository->create([
            'employee_id'   => $employee_id,
            'document_name' => $document_name,
            'document_type' => $document_type,
            'file_url'      => $file_url,
            'status'        => 'Active',
        ], ['%d', '%s', '%s', '%s', '%s']);

        if (!$id) {
            return $this->error('Failed to add document record.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCUMENT_CREATE', "Added document '$document_name' for employee_id: $employee_id");
        return $this->success('Document registered.', $this->documentRepository->findById($id), 201);
    }

    /**
     * PUT /documents/:id
     * Update document (Admin / Manager only can change status)
     */
    public function update(WP_REST_Request $request) {
        $id  = intval($request->get_param('id'));
        $doc = $this->documentRepository->findById($id);
        if (!$doc) {
            return $this->error('Document not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data   = [];
        $fmts   = [];

        if (isset($params['document_name'])) {
            $data['document_name'] = sanitize_text_field($params['document_name']);
            $fmts[] = '%s';
        }
        if (isset($params['document_type'])) {
            $data['document_type'] = sanitize_text_field($params['document_type']);
            $fmts[] = '%s';
        }
        if (isset($params['file_url'])) {
            $data['file_url'] = esc_url_raw($params['file_url']);
            $fmts[] = '%s';
        }
        if (isset($params['status'])) {
            $allowed_statuses = ['Active', 'Inactive', 'Pending Verification', 'Verified'];
            $data['status'] = in_array($params['status'], $allowed_statuses) ? $params['status'] : 'Active';
            $fmts[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No fields to update.');
        }

        $this->documentRepository->update($id, $data, $fmts);
        AuthService::logActivity(get_current_user_id(), 'DOCUMENT_UPDATE', "Updated document ID: $id");
        return $this->success('Document updated.', $this->documentRepository->findById($id));
    }

    /**
     * DELETE /documents/:id
     * Delete a document record (Admin / Manager)
     */
    public function delete(WP_REST_Request $request) {
        $id  = intval($request->get_param('id'));
        $doc = $this->documentRepository->findById($id);
        if (!$doc) {
            return $this->error('Document not found.', [], 404);
        }

        $this->documentRepository->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DOCUMENT_DELETE', "Deleted document ID: $id");
        return $this->success('Document removed.');
    }
}
