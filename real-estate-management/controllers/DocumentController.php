<?php
namespace RealEstateManagementApi\Controllers;

use RealEstateManagementApi\Repositories\DocumentRepository;
use RealEstateManagementApi\Services\AuthService;
use WP_REST_Request;

class DocumentController extends BaseController {
    private $documentRepository;

    public function __construct() {
        $this->documentRepository = new DocumentRepository();
    }

    /**
     * GET /documents
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'related_id', 'related_type', 'created_at'];
        $search_fields = ['file_name', 'related_type', 'file_type'];
        
        $extra_filters = [];
        if (isset($params['related_id'])) {
            $extra_filters['related_id'] = intval($params['related_id']);
        }
        if (isset($params['related_type'])) {
            $extra_filters['related_type'] = sanitize_text_field($params['related_type']);
        }

        $results = $this->documentRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Documents retrieved successfully.', $results);
    }

    /**
     * POST /documents
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['related_id']) || empty($params['related_type']) || empty($params['file_name']) || empty($params['file_url'])) {
            return $this->error('Validation failed: related_id, related_type, file_name, and file_url are required.');
        }

        $data = [
            'related_id' => intval($params['related_id']),
            'related_type' => sanitize_text_field($params['related_type']),
            'file_name' => sanitize_text_field($params['file_name']),
            'file_url' => sanitize_text_field($params['file_url']),
            'file_type' => sanitize_text_field($params['file_type'] ?? 'PDF'),
            'media_id' => isset($params['media_id']) ? intval($params['media_id']) : 0
        ];

        $formats = ['%d', '%s', '%s', '%s', '%s', '%d'];
        $inserted_id = $this->documentRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create document record.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCUMENT_CREATE', "Registered document ID $inserted_id ($params[file_name]) for $params[related_type] ID $params[related_id]");

        return $this->success('Document record created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * DELETE /documents/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $doc = $this->documentRepository->findById($id);

        if (!$doc) {
            return $this->error('Document not found.', [], 404);
        }

        // Delete from media library if media_id is provided
        if (!empty($doc['media_id'])) {
            wp_delete_attachment(intval($doc['media_id']), true);
        }

        $deleted = $this->documentRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete document record.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCUMENT_DELETE', "Soft deleted document ID: $id ($doc[file_name])");

        return $this->success('Document deleted successfully.');
    }
}
