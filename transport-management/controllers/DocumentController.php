<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Repositories\DocumentRepository;
use TransportManagementApi\Services\AuthService;
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
        $allowed_sorts = ['id', 'entity_type', 'entity_id', 'document_type', 'expiry_date', 'created_at'];
        $search_fields = ['document_type', 'file_url'];
        
        $extra_filters = [];
        if (isset($params['entity_type'])) {
            $extra_filters['entity_type'] = sanitize_text_field($params['entity_type']);
        }
        if (isset($params['entity_id'])) {
            $extra_filters['entity_id'] = intval($params['entity_id']);
        }
        if (isset($params['document_type'])) {
            $extra_filters['document_type'] = sanitize_text_field($params['document_type']);
        }

        $results = $this->documentRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Documents retrieved successfully.', $results);
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

        return $this->success('Document retrieved successfully.', $doc);
    }

    /**
     * POST /documents
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['entity_type']) || empty($params['entity_id']) || empty($params['document_type']) || empty($params['file_url'])) {
            return $this->error('Validation failed: entity_type, entity_id, document_type, and file_url are required.');
        }

        $data = [
            'entity_type' => sanitize_text_field($params['entity_type']),
            'entity_id' => intval($params['entity_id']),
            'document_type' => sanitize_text_field($params['document_type']),
            'file_url' => sanitize_text_field($params['file_url']),
            'expiry_date' => !empty($params['expiry_date']) ? sanitize_text_field($params['expiry_date']) : null
        ];

        $formats = ['%s', '%d', '%s', '%s', '%s'];
        $inserted_id = $this->documentRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to log document.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCUMENT_CREATE', "Added document type {$data['document_type']} for {$data['entity_type']} ID {$data['entity_id']}");

        return $this->success('Document details logged successfully.', array_merge(['id' => $inserted_id], $data), 201);
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

        $deleted = $this->documentRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete document.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCUMENT_DELETE', "Soft deleted document ID: $id type: $doc[document_type]");

        return $this->success('Document deleted successfully.');
    }
}
