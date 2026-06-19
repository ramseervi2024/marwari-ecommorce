<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\AssetRepository;
use WorkspaceErpApi\Repositories\AssetAllocationRepository;
use WP_REST_Request;

class AssetController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new AssetRepository();
    }

    public function index(WP_REST_Request $request) {
        return $this->success('Assets fetched successfully', $this->repository->findAll($request->get_params(), ['id', 'asset_code', 'asset_name'], ['asset_code', 'asset_name']));
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['asset_name'])) return $this->error('asset_name is required.');

        $code = isset($params['asset_code']) && !empty($params['asset_code']) ? sanitize_text_field($params['asset_code']) : 'AST-' . rand(1000, 9999);
        $data = [
            'asset_code' => $code,
            'asset_name' => sanitize_text_field($params['asset_name']),
            'category' => isset($params['category']) ? sanitize_text_field($params['category']) : 'General',
            'building_id' => isset($params['building_id']) && $params['building_id'] !== '' ? intval($params['building_id']) : null,
            'floor_id' => isset($params['floor_id']) && $params['floor_id'] !== '' ? intval($params['floor_id']) : null,
            'purchase_date' => isset($params['purchase_date']) && $params['purchase_date'] !== '' ? sanitize_text_field($params['purchase_date']) : null,
            'purchase_cost' => isset($params['purchase_cost']) ? floatval($params['purchase_cost']) : 0.00,
            'current_value' => isset($params['current_value']) ? floatval($params['current_value']) : 0.00,
            'warranty_expiry' => isset($params['warranty_expiry']) && $params['warranty_expiry'] !== '' ? sanitize_text_field($params['warranty_expiry']) : null,
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->repository->create($data, ['%s', '%s', '%s', '%d', '%d', '%s', '%f', '%f', '%s', '%s', '%s', '%s']);
        return $this->success('Asset registered successfully', array_merge(['id' => $id], $data), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $asset = $this->repository->findById($id);
        if (!$asset) return $this->error('Asset not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['asset_name'])) { $update['asset_name'] = sanitize_text_field($params['asset_name']); $formats[] = '%s'; }
        if (isset($params['category'])) { $update['category'] = sanitize_text_field($params['category']); $formats[] = '%s'; }
        if (isset($params['building_id'])) { $update['building_id'] = $params['building_id'] !== '' ? intval($params['building_id']) : null; $formats[] = '%d'; }
        if (isset($params['floor_id'])) { $update['floor_id'] = $params['floor_id'] !== '' ? intval($params['floor_id']) : null; $formats[] = '%d'; }
        if (isset($params['purchase_date'])) { $update['purchase_date'] = $params['purchase_date'] !== '' ? sanitize_text_field($params['purchase_date']) : null; $formats[] = '%s'; }
        if (isset($params['purchase_cost'])) { $update['purchase_cost'] = floatval($params['purchase_cost']); $formats[] = '%f'; }
        if (isset($params['current_value'])) { $update['current_value'] = floatval($params['current_value']); $formats[] = '%f'; }
        if (isset($params['warranty_expiry'])) { $update['warranty_expiry'] = $params['warranty_expiry'] !== '' ? sanitize_text_field($params['warranty_expiry']) : null; $formats[] = '%s'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');

        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->repository->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update asset.');
        return $this->success('Asset updated successfully', $this->repository->findById($id));
    }

    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $asset = $this->repository->findById($id);
        if (!$asset) return $this->error('Asset not found.', [], 404);

        $this->repository->delete($id);
        return $this->success('Asset deleted successfully');
    }

    public function indexAllocations(WP_REST_Request $request) {
        $allocationRepo = new AssetAllocationRepository();
        return $this->success('Asset allocations fetched successfully', $allocationRepo->findAll($request->get_params(), ['id', 'allocated_date'], []));
    }

    public function createAllocation(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['asset_id'])) return $this->error('asset_id is required.');

        $allocationRepo = new AssetAllocationRepository();
        $data = [
            'asset_id' => intval($params['asset_id']),
            'allocated_to' => isset($params['allocated_to']) ? sanitize_text_field($params['allocated_to']) : '',
            'client_id' => isset($params['client_id']) && $params['client_id'] !== '' ? intval($params['client_id']) : null,
            'building_id' => isset($params['building_id']) && $params['building_id'] !== '' ? intval($params['building_id']) : null,
            'floor_id' => isset($params['floor_id']) && $params['floor_id'] !== '' ? intval($params['floor_id']) : null,
            'allocated_date' => isset($params['allocated_date']) ? sanitize_text_field($params['allocated_date']) : current_time('Y-m-d'),
            'return_date' => isset($params['return_date']) && $params['return_date'] !== '' ? sanitize_text_field($params['return_date']) : null,
            'status' => 'ALLOCATED',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $allocationRepo->create($data, ['%d', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s']);
        if (!$id) return $this->error('Failed to register asset allocation.');
        return $this->success('Asset allocated successfully', array_merge(['id' => $id], $data), 201);
    }

    public function updateAllocation(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $allocationRepo = new AssetAllocationRepository();
        $allocation = $allocationRepo->findById($id);
        if (!$allocation) return $this->error('Allocation record not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['allocated_to'])) { $update['allocated_to'] = sanitize_text_field($params['allocated_to']); $formats[] = '%s'; }
        if (isset($params['client_id'])) { $update['client_id'] = $params['client_id'] !== '' ? intval($params['client_id']) : null; $formats[] = '%d'; }
        if (isset($params['building_id'])) { $update['building_id'] = $params['building_id'] !== '' ? intval($params['building_id']) : null; $formats[] = '%d'; }
        if (isset($params['floor_id'])) { $update['floor_id'] = $params['floor_id'] !== '' ? intval($params['floor_id']) : null; $formats[] = '%d'; }
        if (isset($params['allocated_date'])) { $update['allocated_date'] = sanitize_text_field($params['allocated_date']); $formats[] = '%s'; }
        if (isset($params['return_date'])) { $update['return_date'] = $params['return_date'] !== '' ? sanitize_text_field($params['return_date']) : null; $formats[] = '%s'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');

        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $allocationRepo->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update asset allocation.');
        return $this->success('Asset allocation updated successfully', $allocationRepo->findById($id));
    }

    public function deleteAllocation(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $allocationRepo = new AssetAllocationRepository();
        $allocation = $allocationRepo->findById($id);
        if (!$allocation) return $this->error('Allocation record not found.', [], 404);

        $allocationRepo->delete($id);
        return $this->success('Asset allocation deleted successfully');
    }
}
