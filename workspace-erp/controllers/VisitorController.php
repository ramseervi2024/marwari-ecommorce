<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\VisitorRepository;
use WorkspaceErpApi\Services\AuthService;
use WP_REST_Request;

class VisitorController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new VisitorRepository();
    }

    public function index(WP_REST_Request $request) {
        return $this->success('Visitors list fetched successfully', $this->repository->findAll($request->get_params(), ['id', 'visitor_name', 'status'], ['visitor_name', 'company', 'mobile']));
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['visitor_name']) || empty($params['mobile'])) return $this->error('visitor_name and mobile are required.');

        $pass = 'VIS-' . strtoupper(substr(md5(time() . rand()), 0, 8));
        $data = [
            'visitor_name' => sanitize_text_field($params['visitor_name']),
            'company' => isset($params['company']) ? sanitize_text_field($params['company']) : '',
            'mobile' => sanitize_text_field($params['mobile']),
            'email' => isset($params['email']) ? sanitize_email($params['email']) : '',
            'visit_purpose' => isset($params['visit_purpose']) ? sanitize_text_field($params['visit_purpose']) : '',
            'host_client_id' => isset($params['host_client_id']) ? intval($params['host_client_id']) : null,
            'host_name' => isset($params['host_name']) ? sanitize_text_field($params['host_name']) : '',
            'building_id' => isset($params['building_id']) ? intval($params['building_id']) : null,
            'pass_code' => $pass,
            'status' => 'PENDING',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s']);
        if (!$id) {
            return $this->error('Failed to register visitor pass in database.');
        }
        return $this->success('Visitor registered successfully', array_merge(['id' => $id], $data), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $visitor = $this->repository->findById($id);
        if (!$visitor) return $this->error('Visitor not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['status'])) { 
            $update['status'] = sanitize_text_field($params['status']); 
            $formats[] = '%s'; 
            if ($update['status'] === 'CHECKED_IN') {
                $update['check_in'] = current_time('mysql');
                $formats[] = '%s';
            } elseif ($update['status'] === 'CHECKED_OUT') {
                $update['check_out'] = current_time('mysql');
                $formats[] = '%s';
            }
        }
        if (isset($params['visitor_name'])) { $update['visitor_name'] = sanitize_text_field($params['visitor_name']); $formats[] = '%s'; }
        if (isset($params['company'])) { $update['company'] = sanitize_text_field($params['company']); $formats[] = '%s'; }
        if (isset($params['mobile'])) { $update['mobile'] = sanitize_text_field($params['mobile']); $formats[] = '%s'; }
        if (isset($params['email'])) { $update['email'] = sanitize_email($params['email']); $formats[] = '%s'; }
        if (isset($params['visit_purpose'])) { $update['visit_purpose'] = sanitize_text_field($params['visit_purpose']); $formats[] = '%s'; }
        if (isset($params['host_name'])) { $update['host_name'] = sanitize_text_field($params['host_name']); $formats[] = '%s'; }
        if (isset($params['building_id'])) { $update['building_id'] = intval($params['building_id']); $formats[] = '%d'; }

        if (empty($update)) return $this->error('No fields to update.');
        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->repository->update($id, $update, $formats);
        return $this->success('Visitor updated successfully', $this->repository->findById($id));
     }

     public function delete(WP_REST_Request $request) {
         $id = (int)$request->get_param('id');
         $visitor = $this->repository->findById($id);
         if (!$visitor) return $this->error('Visitor not found.', [], 404);

         $this->repository->delete($id);
         AuthService::logActivity(get_current_user_id(), 'DELETE_VISITOR', "Soft deleted visitor ID: $id");
         return $this->success('Visitor deleted successfully');
     }
}
