<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\BomRepository;
use WP_REST_Request;

class BomController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new BomRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        
        // Decode JSON accessories for readability
        foreach ($items as &$item) {
            if (!empty($item['accessories_requirement'])) {
                $item['accessories_requirement'] = json_decode($item['accessories_requirement'], true);
            }
        }
        
        return $this->success('BOM listings retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('BOM item not found.', [], 404);
        }
        
        if (!empty($item['accessories_requirement'])) {
            $item['accessories_requirement'] = json_decode($item['accessories_requirement'], true);
        }
        
        return $this->success('BOM retrieved successfully.', $item);
    }

    public function getByProduct(WP_REST_Request $request) {
        $product_id = sanitize_text_field($request->get_param('product_id'));
        $items = $this->repo->getByProduct($product_id);
        
        foreach ($items as &$item) {
            if (!empty($item['accessories_requirement'])) {
                $item['accessories_requirement'] = json_decode($item['accessories_requirement'], true);
            }
        }
        
        return $this->success('BOM retrieve for product successful.', $items);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['product_id']) || empty($params['fabric_id']) || empty($params['fabric_requirement'])) {
            return $this->error('Validation failed: product_id, fabric_id and fabric_requirement are required.');
        }

        $fabric_id = intval($params['fabric_id']);
        $fabric_req = floatval($params['fabric_requirement']);
        $accessories = $params['accessories_requirement'] ?? [];
        $cost = floatval($params['estimated_cost'] ?? 0);

        $db_data = [
            'product_id' => sanitize_text_field($params['product_id']),
            'fabric_id' => $fabric_id,
            'fabric_requirement' => $fabric_req,
            'accessories_requirement' => is_array($accessories) ? json_encode($accessories) : sanitize_text_field($accessories),
            'estimated_cost' => $cost,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->repo->create($db_data);
        if (!$id) {
            return $this->error('Failed to register BOM formulation.');
        }

        return $this->success('BOM formulation created successfully.', array_merge(['id' => $id], $db_data), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('BOM formulation not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['product_id'])) $updates['product_id'] = sanitize_text_field($params['product_id']);
        if (isset($params['fabric_id'])) $updates['fabric_id'] = intval($params['fabric_id']);
        if (isset($params['fabric_requirement'])) $updates['fabric_requirement'] = floatval($params['fabric_requirement']);
        if (isset($params['accessories_requirement'])) {
            $updates['accessories_requirement'] = is_array($params['accessories_requirement']) ? json_encode($params['accessories_requirement']) : sanitize_text_field($params['accessories_requirement']);
        }
        if (isset($params['estimated_cost'])) $updates['estimated_cost'] = floatval($params['estimated_cost']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update BOM formulation.');
        }

        return $this->success('BOM formulation updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('BOM formulation not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete BOM formulation.');
        }
        return $this->success('BOM formulation deleted successfully.');
    }
}
