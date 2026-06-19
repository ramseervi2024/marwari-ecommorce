<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\AssetRepository;
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

        $code = 'AST-' . rand(1000, 9999);
        $data = [
            'asset_code' => $code,
            'asset_name' => sanitize_text_field($params['asset_name']),
            'category' => isset($params['category']) ? sanitize_text_field($params['category']) : 'General',
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s']);
        return $this->success('Asset registered successfully', array_merge(['id' => $id], $data), 201);
    }
}
