<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use WP_REST_Request;

class MediaController extends BaseController {

    /**
     * POST /media/upload
     */
    public function upload(WP_REST_Request $request) {
        // Required for media upload functions in WordPress
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $files = $request->get_file_params();

        if (empty($files['file'])) {
            return $this->error('Validation failed: file parameter is required.');
        }

        // Handle upload
        $attachment_id = media_handle_upload('file', 0);

        if (is_wp_error($attachment_id)) {
            return $this->error('Upload failed: ' . $attachment_id->get_error_message());
        }

        $url = wp_get_attachment_url($attachment_id);
        $meta = wp_get_attachment_metadata($attachment_id);

        return $this->success('Media uploaded successfully.', [
            'media_id' => $attachment_id,
            'url' => $url,
            'file_name' => basename($url),
            'mime_type' => get_post_mime_type($attachment_id),
            'meta' => $meta ?: []
        ], 201);
    }
}
