<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\JobWorkRepository;
use JewelleryManagementApi\Repositories\KarigarRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class JobWorkController extends BaseController {
    private $repo;
    private $karigarRepo;

    public function __construct() {
        $this->repo = new JobWorkRepository();
        $this->karigarRepo = new KarigarRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Job work assignments retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Job work assignment not found.', [], 404);
        }
        return $this->success('Job work assignment retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['karigar_id']) || empty($params['product_id'])) {
            return $this->error('Validation failed: karigar_id and product_id are required.');
        }

        $karigar_id = intval($params['karigar_id']);
        $karigar = $this->karigarRepo->find($karigar_id);
        if (!$karigar) {
            return $this->error('Karigar not found.');
        }

        $params['job_number'] = sanitize_text_field($params['job_number'] ?? 'JOB-' . rand(1000, 9999));
        $params['karigar_id'] = $karigar_id;
        $params['product_id'] = intval($params['product_id']);
        $params['metal_weight'] = floatval($params['metal_weight'] ?? 0);
        $params['expected_completion'] = sanitize_text_field($params['expected_completion'] ?? '');
        $params['actual_completion'] = null;
        
        $labor_cost = floatval($params['labor_cost'] ?? 0);
        if ($labor_cost <= 0) {
            $labor_cost = $params['metal_weight'] * floatval($karigar['per_gram_rate']);
        }
        $params['labor_cost'] = $labor_cost;
        $params['status'] = sanitize_text_field($params['status'] ?? 'Assigned');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create job work assignment.');
        }

        AuthService::logActivity(get_current_user_id(), 'JOB_WORK_CREATE', "Assigned job {$params['job_number']} to Karigar {$karigar['name']}, weight allocated: {$params['metal_weight']}g");

        return $this->success('Job work assignment created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Job work assignment not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['karigar_id'])) $updates['karigar_id'] = intval($params['karigar_id']);
        if (isset($params['product_id'])) $updates['product_id'] = intval($params['product_id']);
        if (isset($params['metal_weight'])) $updates['metal_weight'] = floatval($params['metal_weight']);
        if (isset($params['expected_completion'])) $updates['expected_completion'] = sanitize_text_field($params['expected_completion']);
        if (isset($params['labor_cost'])) $updates['labor_cost'] = floatval($params['labor_cost']);
        
        if (isset($params['status'])) {
            $updates['status'] = sanitize_text_field($params['status']);
            if (in_array($updates['status'], ['Completed', 'Delivered'])) {
                $updates['actual_completion'] = current_time('mysql');
            }
        }
        
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update job work assignment.');
        }

        AuthService::logActivity(get_current_user_id(), 'JOB_WORK_UPDATE', "Updated job work ID $id status to " . ($updates['status'] ?? $item['status']));

        return $this->success('Job work assignment updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Job work assignment not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete job work assignment.');
        }

        AuthService::logActivity(get_current_user_id(), 'JOB_WORK_DELETE', "Deleted job work assignment: {$item['job_number']}");

        return $this->success('Job work assignment deleted successfully.');
    }
}
