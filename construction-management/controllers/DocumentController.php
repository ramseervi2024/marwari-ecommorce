<?php
namespace ConstructionManagementApi\Controllers;

use ConstructionManagementApi\Repositories\DocumentRepository;
use ConstructionManagementApi\Services\AuthService;
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
        $allowed_sorts = ['id', 'related_id', 'related_type', 'document_type', 'created_at'];
        $search_fields = ['related_type', 'document_type', 'file_url'];

        $extra_filters = [];
        if (isset($params['related_id'])) {
            $extra_filters['related_id'] = intval($params['related_id']);
        }
        if (isset($params['related_type'])) {
            $extra_filters['related_type'] = sanitize_text_field($params['related_type']);
        }
        if (isset($params['document_type'])) {
            $extra_filters['document_type'] = sanitize_text_field($params['document_type']);
        }

        $results = $this->documentRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Documents list retrieved successfully.', $results);
    }

    /**
     * POST /documents
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['related_id']) || empty($params['related_type']) || empty($params['document_type']) || empty($params['file_url'])) {
            return $this->error('Validation failed: related_id, related_type, document_type, and file_url are required.');
        }

        $data = [
            'related_id' => intval($params['related_id']),
            'related_type' => sanitize_text_field($params['related_type']),
            'document_type' => sanitize_text_field($params['document_type']),
            'file_url' => esc_url_raw($params['file_url']),
            'media_id' => isset($params['media_id']) ? intval($params['media_id']) : 0
        ];

        $formats = ['%d', '%s', '%s', '%s', '%d'];
        $inserted_id = $this->documentRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to link document.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCUMENT_LINK', "Linked document ID: $inserted_id type: $data[document_type] for relation: $data[related_type] ID $data[related_id]");

        return $this->success('Document linked successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * DELETE /documents/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $document = $this->documentRepository->findById($id);

        if (!$document) {
            return $this->error('Document link not found.', [], 404);
        }

        $deleted = $this->documentRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete document link.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCUMENT_DELETE', "Soft deleted document ID: $id type: $document[document_type]");

        return $this->success('Document link deleted successfully.');
    }
}
