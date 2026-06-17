<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\KarigarRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class KarigarController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new KarigarRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Karigars list retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Karigar not found.', [], 404);
        }
        return $this->success('Karigar retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['name'])) {
            return $this->error('Validation failed: name is required.');
        }

        $params['karigar_code'] = sanitize_text_field($params['karigar_code'] ?? 'KARI-' . rand(100, 999));
        $params['name'] = sanitize_text_field($params['name']);
        $params['mobile'] = sanitize_text_field($params['mobile'] ?? '');
        $params['specialization'] = sanitize_text_field($params['specialization'] ?? 'Gold Work');
        $params['daily_rate'] = floatval($params['daily_rate'] ?? 0);
        $params['per_gram_rate'] = floatval($params['per_gram_rate'] ?? 0);
        $params['status'] = sanitize_text_field($params['status'] ?? 'ACTIVE');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Karigar.');
        }

        AuthService::logActivity(get_current_user_id(), 'KARIGAR_CREATE', "Created Karigar: {$params['name']} [{$params['karigar_code']}]");

        return $this->success('Karigar created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Karigar not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['karigar_code'])) $updates['karigar_code'] = sanitize_text_field($params['karigar_code']);
        if (isset($params['name'])) $updates['name'] = sanitize_text_field($params['name']);
        if (isset($params['mobile'])) $updates['mobile'] = sanitize_text_field($params['mobile']);
        if (isset($params['specialization'])) $updates['specialization'] = sanitize_text_field($params['specialization']);
        if (isset($params['daily_rate'])) $updates['daily_rate'] = floatval($params['daily_rate']);
        if (isset($params['per_gram_rate'])) $updates['per_gram_rate'] = floatval($params['per_gram_rate']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Karigar.');
        }

        AuthService::logActivity(get_current_user_id(), 'KARIGAR_UPDATE', "Updated Karigar ID $id");

        return $this->success('Karigar updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Karigar not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Karigar.');
        }

        AuthService::logActivity(get_current_user_id(), 'KARIGAR_DELETE', "Deleted Karigar: {$item['name']}");

        return $this->success('Karigar deleted successfully.');
    }
}
