<?php
namespace FleetTrackPro\Controllers;

use FleetTrackPro\Services\AuthService;
use WP_REST_Request;

class MediaController extends BaseController {
    
    /**
     * POST /media/upload
     */
    public function upload(WP_REST_Request $request) {
        global $wpdb;

        // Check if file is uploaded
        if (empty($_FILES['file'])) {
            return $this->error('Validation failed: No file upload detected under key "file".');
        }

        $file = $_FILES['file'];
        
        // 1. Validate size (20MB limit)
        $max_size = 20 * 1024 * 1024; // 20 MB
        if ($file['size'] > $max_size) {
            return $this->error('File upload limit exceeded: Maximum size allowed is 20MB.');
        }

        // 2. Validate extensions
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_exts)) {
            return $this->error('Validation failed: Supported formats are JPG, JPEG, PNG, WEBP, PDF.');
        }

        // Load WordPress upload handlers
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        // 3. Perform WordPress media library insert
        $attachment_id = media_handle_upload('file', 0);

        if (is_wp_error($attachment_id)) {
            return $this->error('WordPress Media upload failed: ' . $attachment_id->get_error_message());
        }

        $file_url = wp_get_attachment_url($attachment_id);

        // 4. Save metadata to wp_fleet_documents if related parameters exist
        $related_type = $request->get_param('related_type'); // vehicle, driver
        $related_id = $request->get_param('related_id');
        $doc_type = $request->get_param('document_type'); // RC, Insurance, Permit, Fitness, Pollution, License, Aadhaar, PAN, Medical
        $expiry = $request->get_param('expiry_date');

        $doc_id = null;
        if (!empty($related_type) && !empty($related_id) && !empty($doc_type)) {
            $table_docs = $wpdb->prefix . 'fleet_documents';
            $wpdb->insert(
                $table_docs,
                [
                    'related_type' => sanitize_text_field($related_type),
                    'related_id' => (int)$related_id,
                    'document_type' => sanitize_text_field($doc_type),
                    'file_url' => esc_url_raw($file_url),
                    'media_id' => $attachment_id,
                    'expiry_date' => !empty($expiry) ? sanitize_text_field($expiry) : null,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ],
                ['%s', '%d', '%s', '%s', '%d', '%s', '%s', '%s']
            );
            $doc_id = (int)$wpdb->insert_id;
        }

        AuthService::logActivity(
            get_current_user_id(),
            'MEDIA_UPLOAD',
            "Uploaded file '{$file['name']}' to WordPress Library (Attachment ID: $attachment_id)"
        );

        return $this->success('Media file uploaded successfully', [
            'attachment_id' => $attachment_id,
            'file_url' => $file_url,
            'document_record_id' => $doc_id
        ], 201);
    }
}
