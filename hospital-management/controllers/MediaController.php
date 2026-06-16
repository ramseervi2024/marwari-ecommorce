<?php
namespace HospitalManagementApi\Controllers;

use WP_REST_Request;

class MediaController extends BaseController {

    public function upload(WP_REST_Request $request) {
        if (!function_exists('media_handle_upload')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
        }

        if (empty($_FILES['file'])) {
            return $this->error('Validation failed: file parameter is required in multipart form data.');
        }

        // Limit file size to 20MB
        $max_size = 20 * 1024 * 1024;
        if ($_FILES['file']['size'] > $max_size) {
            return $this->error('Validation failed: File size exceeds the maximum limit of 20 MB.');
        }

        $attachment_id = media_handle_upload('file', 0);

        if (is_wp_error($attachment_id)) {
            return $this->error('Media upload failed: ' . $attachment_id->get_error_message());
        }

        $url = wp_get_attachment_url($attachment_id);

        return $this->success('Media uploaded successfully.', [
            'media_id' => $attachment_id,
            'file_url' => $url
        ], 201);
    }
}
