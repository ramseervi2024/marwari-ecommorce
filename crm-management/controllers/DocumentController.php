<?php
namespace CrmManagementApi\Controllers;

use CrmManagementApi\Repositories\DocumentRepository;
use CrmManagementApi\Repositories\LeadRepository;
use CrmManagementApi\Repositories\CustomerRepository;
use CrmManagementApi\Services\AuthService;
use WP_REST_Request;

class DocumentController extends BaseController {
    private $documentRepository;
    private $leadRepository;
    private $customerRepository;

    public function __construct() {
        $this->documentRepository = new DocumentRepository();
        $this->leadRepository     = new LeadRepository();
        $this->customerRepository = new CustomerRepository();
    }

    /**
     * GET /documents
     */
    public function getDocuments(WP_REST_Request $request) {
        $params = $request->get_params();
        $current_user = wp_get_current_user();

        $allowed_sorts = ['id', 'document_name', 'status', 'uploaded_at'];
        $extra_filters = [];

        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        // Customer restriction: see only their own documents
        if (in_array('crm_customer', (array)$current_user->roles)) {
            $cust = $this->customerRepository->findByUserId($current_user->ID);
            if (!$cust) {
                return $this->success('Documents list (empty).', [
                    'total' => 0, 'page' => 1, 'limit' => 10, 'pages' => 0, 'data' => []
                ]);
            }
            $extra_filters['customer_id'] = $cust['id'];
        } elseif (isset($params['customer_id'])) {
            $extra_filters['customer_id'] = intval($params['customer_id']);
        }

        if (isset($params['lead_id'])) {
            $extra_filters['lead_id'] = intval($params['lead_id']);
        }

        $results = $this->documentRepository->findAll($params, $allowed_sorts, ['document_name'], $extra_filters);

        // Map titles
        foreach ($results['data'] as &$row) {
            if ($row['lead_id']) {
                $lead = $this->leadRepository->findById($row['lead_id']);
                $row['lead_name'] = $lead ? $lead['first_name'] . ' ' . $lead['last_name'] : '';
            } else {
                $row['lead_name'] = '';
            }

            if ($row['customer_id']) {
                $cust = $this->customerRepository->findById($row['customer_id']);
                $row['company_name'] = $cust ? $cust['company_name'] : '';
            } else {
                $row['company_name'] = '';
            }
        }

        return $this->success('Documents retrieved.', $results);
    }

    /**
     * POST /documents
     */
    public function createDocument(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['document_name']) || empty($params['file_url'])) {
            return $this->error('document_name and file_url are required.');
        }

        $data = [
            'lead_id'       => !empty($params['lead_id']) ? intval($params['lead_id']) : null,
            'customer_id'   => !empty($params['customer_id']) ? intval($params['customer_id']) : null,
            'document_name' => sanitize_text_field($params['document_name']),
            'file_url'      => esc_url_raw($params['file_url']),
            'status'        => sanitize_text_field($params['status'] ?? 'Active'),
        ];

        $formats = ['%d', '%d', '%s', '%s', '%s'];

        $id = $this->documentRepository->create($data, $formats);
        if (!$id) {
            return $this->error('Failed to register document.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCUMENT_CREATE', "Registered document: $data[document_name]");

        return $this->success('Document registered successfully.', $this->documentRepository->findById($id), 201);
    }

    /**
     * PUT /documents/{id}
     */
    public function updateDocument(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $document = $this->documentRepository->findById($id);

        if (!$document) {
            return $this->error('Document not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['status'])) {
            $data['status'] = sanitize_text_field($params['status']);
            $formats[] = '%s';
        }

        if (isset($params['document_name'])) {
            $data['document_name'] = sanitize_text_field($params['document_name']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->documentRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update document.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCUMENT_UPDATE', "Updated document ID: $id ($document[document_name])");

        return $this->success('Document updated successfully.', $this->documentRepository->findById($id));
    }

    /**
     * DELETE /documents/{id}
     */
    public function deleteDocument(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $document = $this->documentRepository->findById($id);

        if (!$document) {
            return $this->error('Document not found.', [], 404);
        }

        $deleted = $this->documentRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete document.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCUMENT_DELETE', "Deleted document ID: $id ($document[document_name])");

        return $this->success('Document deleted successfully.');
    }

    /**
     * POST /media/upload
     * Upload file to WordPress media library and register as document
     */
    public function uploadMedia(WP_REST_Request $request) {
        if (!function_exists('wp_handle_upload')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        if (empty($_FILES['file'])) {
            return $this->error('No file uploaded. Use multipart/form-data with key "file".');
        }

        $file = $_FILES['file'];
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        if (!in_array($file['type'], $allowed_types)) {
            return $this->error('File type not allowed. Supported: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX.');
        }

        if ($file['size'] > 20 * 1024 * 1024) {
            return $this->error('File size exceeds 20 MB limit.');
        }

        $upload = wp_handle_upload($file, ['test_form' => false]);

        if (isset($upload['error'])) {
            return $this->error('File upload failed: ' . $upload['error']);
        }

        // Insert into media library
        $attachment_id = wp_insert_attachment([
            'post_mime_type' => $file['type'],
            'post_title'     => sanitize_file_name($file['name']),
            'post_status'    => 'inherit',
        ], $upload['file']);

        wp_generate_attachment_metadata($attachment_id, $upload['file']);

        $lead_id = !empty($_POST['lead_id']) ? intval($_POST['lead_id']) : null;
        $customer_id = !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
        $doc_name = sanitize_text_field($_POST['document_name'] ?? basename($file['name']));

        // Register as CRM document
        $this->documentRepository->create([
            'lead_id'       => $lead_id,
            'customer_id'   => $customer_id,
            'document_name' => $doc_name,
            'file_url'      => $upload['url'],
            'status'        => 'Active',
        ], ['%d', '%d', '%s', '%s', '%s']);

        AuthService::logActivity(get_current_user_id(), 'MEDIA_UPLOAD', "Uploaded file: $doc_name");

        return $this->success('File uploaded successfully.', [
            'attachment_id' => $attachment_id,
            'url'           => $upload['url'],
            'filename'      => basename($upload['file']),
            'document_name' => $doc_name,
        ], 201);
    }
}
