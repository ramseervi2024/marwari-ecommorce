<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\DiamondRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class DiamondController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new DiamondRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Diamonds inventory retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Diamond record not found.', [], 404);
        }
        return $this->success('Diamond record retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['diamond_code'])) {
            return $this->error('Validation failed: diamond_code is required.');
        }

        $params['diamond_code'] = sanitize_text_field($params['diamond_code']);
        $params['shape'] = sanitize_text_field($params['shape'] ?? '');
        $params['carat'] = floatval($params['carat'] ?? 0);
        $params['clarity'] = sanitize_text_field($params['clarity'] ?? '');
        $params['color'] = sanitize_text_field($params['color'] ?? '');
        $params['certificate_number'] = sanitize_text_field($params['certificate_number'] ?? '');
        $params['purchase_price'] = floatval($params['purchase_price'] ?? 0);
        $params['selling_price'] = floatval($params['selling_price'] ?? 0);
        $params['status'] = sanitize_text_field($params['status'] ?? 'ACTIVE');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create diamond record.');
        }

        AuthService::logActivity(get_current_user_id(), 'DIAMOND_CREATE', "Added diamond: {$params['diamond_code']} - {$params['carat']} Carat");

        return $this->success('Diamond record created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Diamond record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['diamond_code'])) $updates['diamond_code'] = sanitize_text_field($params['diamond_code']);
        if (isset($params['shape'])) $updates['shape'] = sanitize_text_field($params['shape']);
        if (isset($params['carat'])) $updates['carat'] = floatval($params['carat']);
        if (isset($params['clarity'])) $updates['clarity'] = sanitize_text_field($params['clarity']);
        if (isset($params['color'])) $updates['color'] = sanitize_text_field($params['color']);
        if (isset($params['certificate_number'])) $updates['certificate_number'] = sanitize_text_field($params['certificate_number']);
        if (isset($params['purchase_price'])) $updates['purchase_price'] = floatval($params['purchase_price']);
        if (isset($params['selling_price'])) $updates['selling_price'] = floatval($params['selling_price']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update diamond record.');
        }

        AuthService::logActivity(get_current_user_id(), 'DIAMOND_UPDATE', "Updated diamond ID $id");

        return $this->success('Diamond record updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Diamond record not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete diamond record.');
        }

        AuthService::logActivity(get_current_user_id(), 'DIAMOND_DELETE', "Deleted diamond ID $id");

        return $this->success('Diamond record deleted successfully.');
    }
}
