<?php
namespace ManufacturingManagementApi\Controllers;

use WP_REST_Request;

class MediaController extends BaseController {
    
    public function upload(WP_REST_Request $request) {
        if (!function_exists('wp_handle_upload')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        if (empty($_FILES['file'])) {
            return $this->error('No file uploaded.');
        }

        $file = $_FILES['file'];
        $max_size = 20 * 1024 * 1024; // 20 MB

        if ($file['size'] > $max_size) {
            return $this->error('File size exceeds 20MB limit.');
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file['type'], $allowed_types)) {
            return $this->error('Unsupported file format. Please upload JPG, PNG, WEBP, PDF, XLSX, or DOCX.');
        }

        $upload_overrides = ['test_form' => false];
        $movefile = wp_handle_upload($file, $upload_overrides);

        if ($movefile && empty($movefile['error'])) {
            // Register attachment in media library
            $attachment = [
                'guid'           => $movefile['url'],
                'post_mime_type' => $movefile['type'],
                'post_title'     => preg_replace('/\.[^.]+$/', '', basename($file['name'])),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ];

            $attach_id = wp_insert_attachment($attachment, $movefile['file']);
            $attach_data = wp_generate_attachment_metadata($attach_id, $movefile['file']);
            wp_update_attachment_metadata($attach_id, $attach_data);

            return $this->success('File uploaded successfully.', [
                'attachment_id' => $attach_id,
                'url' => $movefile['url']
            ], 201);
        } else {
            return $this->error('File upload failed: ' . ($movefile['error'] ?? 'Unknown error'));
        }
    }
}
