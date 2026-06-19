<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\OccupancyRepository;
use WP_REST_Request;

class OccupancyController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new OccupancyRepository();
    }

    public function index(WP_REST_Request $request) {
        return $this->success('Occupancy list fetched successfully', $this->repository->findAll($request->get_params(), ['id', 'status'], []));
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['building_id']) || empty($params['client_id'])) return $this->error('building_id and client_id are required.');

        $data = [
            'building_id' => intval($params['building_id']),
            'floor_id' => isset($params['floor_id']) ? intval($params['floor_id']) : null,
            'workspace_id' => isset($params['workspace_id']) ? intval($params['workspace_id']) : null,
            'seat_id' => isset($params['seat_id']) ? intval($params['seat_id']) : null,
            'client_id' => intval($params['client_id']),
            'occupied_from' => isset($params['occupied_from']) ? sanitize_text_field($params['occupied_from']) : current_time('Y-m-d'),
            'occupied_to' => isset($params['occupied_to']) ? sanitize_text_field($params['occupied_to']) : null,
            'status' => 'OCCUPIED',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->repository->create($data, ['%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s']);
        return $this->success('Occupancy allocated successfully', array_merge(['id' => $id], $data), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $occ = $this->repository->findById($id);
        if (!$occ) return $this->error('Occupancy not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }
        if (isset($params['occupied_to'])) { $update['occupied_to'] = sanitize_text_field($params['occupied_to']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');
        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->repository->update($id, $update, $formats);
        return $this->success('Occupancy updated successfully', $this->repository->findById($id));
    }

    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $this->repository->delete($id);
        return $this->success('Occupancy released successfully');
    }
}
