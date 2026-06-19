<?php
namespace WholesaleErp\Controllers;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class MediaController extends BaseController {
    public function upload(WP_REST_Request $request) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $files = $request->get_file_params();
        if (empty($files['file'])) {
            return $this->error('No file uploaded.');
        }

        // Upload to WordPress media library
        $attachment_id = media_handle_upload('file', 0);
        if (is_wp_error($attachment_id)) {
            return $this->error($attachment_id->get_error_message());
        }

        $url = wp_get_attachment_url($attachment_id);
        
        // Save in wholesale documents table
        global $wpdb;
        $table = $wpdb->prefix . 'wholesale_documents';
        $wpdb->insert($table, [
            'reference_type' => $request->get_param('reference_type') ?: 'general',
            'reference_id'   => !empty($request->get_param('reference_id')) ? (int)$request->get_param('reference_id') : null,
            'file_name'      => basename($url),
            'file_path'      => $url,
            'file_type'      => get_post_mime_type($attachment_id),
            'file_size'      => isset($files['file']['size']) ? (int)$files['file']['size'] : 0,
            'uploaded_by'    => get_current_user_id(),
        ], ['%s', '%d', '%s', '%s', '%s', '%d', '%d']);

        return $this->success('File uploaded successfully.', [
            'attachment_id' => $attachment_id,
            'url'           => $url,
        ]);
    }
}
