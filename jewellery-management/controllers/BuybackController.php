<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\BuybackRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class BuybackController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new BuybackRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Buyback and exchange entries retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Buyback entry not found.', [], 404);
        }
        return $this->success('Buyback entry retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['customer_id']) || empty($params['metal_type'])) {
            return $this->error('Validation failed: customer_id and metal_type are required.');
        }

        $params['transaction_number'] = sanitize_text_field($params['transaction_number'] ?? 'BUY-' . date('Ymd') . '-' . rand(1000, 9999));
        $params['customer_id'] = intval($params['customer_id']);
        $params['metal_type'] = sanitize_text_field($params['metal_type']);
        $params['purity'] = sanitize_text_field($params['purity'] ?? '');
        $params['weight'] = floatval($params['weight'] ?? 0);
        $params['rate_per_gram'] = floatval($params['rate_per_gram'] ?? 0);
        $params['payout_amount'] = floatval($params['payout_amount'] ?: ($params['weight'] * $params['rate_per_gram']));
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to record buyback entry.');
        }

        AuthService::logActivity(get_current_user_id(), 'BUYBACK_CREATE', "Logged buyback transaction {$params['transaction_number']}, payout calculated: {$params['payout_amount']} INR");

        return $this->success('Buyback entry recorded successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Buyback entry not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['metal_type'])) $updates['metal_type'] = sanitize_text_field($params['metal_type']);
        if (isset($params['purity'])) $updates['purity'] = sanitize_text_field($params['purity']);
        if (isset($params['weight'])) $updates['weight'] = floatval($params['weight']);
        if (isset($params['rate_per_gram'])) $updates['rate_per_gram'] = floatval($params['rate_per_gram']);
        
        $weight = isset($updates['weight']) ? $updates['weight'] : floatval($item['weight']);
        $rate = isset($updates['rate_per_gram']) ? $updates['rate_per_gram'] : floatval($item['rate_per_gram']);
        $updates['payout_amount'] = $weight * $rate;
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update buyback entry.');
        }

        AuthService::logActivity(get_current_user_id(), 'BUYBACK_UPDATE', "Updated buyback ID $id");

        return $this->success('Buyback entry updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Buyback entry not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete buyback entry.');
        }

        AuthService::logActivity(get_current_user_id(), 'BUYBACK_DELETE', "Deleted buyback entry ID $id");

        return $this->success('Buyback entry deleted successfully.');
    }
}
