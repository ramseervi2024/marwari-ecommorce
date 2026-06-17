<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\MetalStockRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class MetalStockController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new MetalStockRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Bullion stock records retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Bullion stock record not found.', [], 404);
        }
        return $this->success('Bullion stock record retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['metal_type']) || empty($params['purity'])) {
            return $this->error('Validation failed: missing metal_type or purity.');
        }

        $params['metal_type'] = sanitize_text_field($params['metal_type']);
        $params['purity'] = sanitize_text_field($params['purity']);
        $params['weight'] = floatval($params['weight'] ?? 0);
        $params['rate_per_gram'] = floatval($params['rate_per_gram'] ?? 0);
        $params['total_value'] = floatval($params['total_value'] ?: ($params['weight'] * $params['rate_per_gram']));
        $params['location'] = sanitize_text_field($params['location'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create bullion stock record.');
        }

        AuthService::logActivity(get_current_user_id(), 'METAL_STOCK_CREATE', "Added bullion stock: {$params['metal_type']} {$params['purity']} - {$params['weight']}g");

        return $this->success('Bullion stock record created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Bullion stock record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['metal_type'])) $updates['metal_type'] = sanitize_text_field($params['metal_type']);
        if (isset($params['purity'])) $updates['purity'] = sanitize_text_field($params['purity']);
        if (isset($params['weight'])) $updates['weight'] = floatval($params['weight']);
        if (isset($params['rate_per_gram'])) $updates['rate_per_gram'] = floatval($params['rate_per_gram']);
        if (isset($params['location'])) $updates['location'] = sanitize_text_field($params['location']);
        
        $weight = isset($updates['weight']) ? $updates['weight'] : floatval($item['weight']);
        $rate = isset($updates['rate_per_gram']) ? $updates['rate_per_gram'] : floatval($item['rate_per_gram']);
        $updates['total_value'] = $weight * $rate;
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update bullion stock record.');
        }

        AuthService::logActivity(get_current_user_id(), 'METAL_STOCK_UPDATE', "Updated bullion stock ID $id");

        return $this->success('Bullion stock record updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Bullion stock record not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete bullion stock record.');
        }

        AuthService::logActivity(get_current_user_id(), 'METAL_STOCK_DELETE', "Deleted bullion stock record ID $id");

        return $this->success('Bullion stock record deleted successfully.');
    }
}
