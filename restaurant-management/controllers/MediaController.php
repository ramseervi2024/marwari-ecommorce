<?php
namespace RestaurantManagementApi\Controllers;

use WP_REST_Request;

class MediaController extends BaseController {
    
    public function upload(WP_REST_Request $request) {
        $files = $request->get_file_params();
        if (empty($files['file'])) {
            return $this->error('No file was uploaded. Please send a multi-part file key named "file".');
        }

        $file = $files['file'];
        
        // Max size: 20MB
        if ($file['size'] > 20 * 1024 * 1024) {
            return $this->error('File size exceeds 20 MB limit.');
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        if (!in_array($file['type'], $allowed_types)) {
            return $this->error('Unsupported file format. Only JPG, PNG, WEBP, and PDF are allowed.');
        }

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $attachment_id = media_handle_upload('file', 0);

        if (is_wp_error($attachment_id)) {
            return $this->error('Media upload failed: ' . $attachment_id->get_error_message());
        }

        $url = wp_get_attachment_url($attachment_id);

        return $this->success('File uploaded successfully.', [
            'attachment_id' => $attachment_id,
            'url' => $url
        ], 201);
    }
}
