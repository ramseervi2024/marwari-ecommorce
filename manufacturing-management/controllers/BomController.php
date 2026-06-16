<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\BomRepository;
use ManufacturingManagementApi\Repositories\RawMaterialRepository;
use WP_REST_Request;

class BomController extends BaseController {
    private $repo;
    private $rawRepo;

    public function __construct() {
        $this->repo = new BomRepository();
        $this->rawRepo = new RawMaterialRepository();
    }

    public function index(WP_REST_Request $request) {
        $items = $this->repo->all();
        // Enrich with raw material details
        foreach ($items as &$item) {
            $mat = $this->rawRepo->find(intval($item['material_id']));
            $item['material_name'] = $mat ? $mat['material_name'] : 'Unknown';
            $item['material_code'] = $mat ? $mat['material_code'] : 'Unknown';
        }
        return $this->success('BOM formulas retrieved successfully.', $items);
    }

    public function getByProduct(WP_REST_Request $request) {
        $product_id = intval($request->get_param('product_id'));
        $items = $this->repo->getByProduct($product_id);
        foreach ($items as &$item) {
            $mat = $this->rawRepo->find(intval($item['material_id']));
            $item['material_name'] = $mat ? $mat['material_name'] : 'Unknown';
            $item['material_code'] = $mat ? $mat['material_code'] : 'Unknown';
        }
        return $this->success('BOM formulas for product retrieved successfully.', $items);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['product_id']) || empty($params['material_id']) || empty($params['required_quantity'])) {
            return $this->error('Validation failed: product_id, material_id, and required_quantity are required.');
        }

        $params['product_id'] = intval($params['product_id']);
        $params['material_id'] = intval($params['material_id']);
        $params['required_quantity'] = floatval($params['required_quantity']);
        $params['unit'] = sanitize_text_field($params['unit'] ?? 'KG');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to register BOM formula.');
        }

        return $this->success('BOM formula registered successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('BOM formula not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['required_quantity'])) $updates['required_quantity'] = floatval($params['required_quantity']);
        if (isset($params['unit'])) $updates['unit'] = sanitize_text_field($params['unit']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update BOM formula.');
        }

        return $this->success('BOM formula updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('BOM formula not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete BOM formula.');
        }
        return $this->success('BOM formula deleted successfully.');
    }
}
