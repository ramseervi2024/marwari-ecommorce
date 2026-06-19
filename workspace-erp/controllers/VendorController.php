<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\VendorRepository;
use WP_REST_Request;

class VendorController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new VendorRepository();
    }

    public function index(WP_REST_Request $request) {
        return $this->success('Vendors fetched successfully', $this->repository->findAll($request->get_params(), ['id', 'vendor_name'], ['vendor_name', 'company_name']));
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['vendor_name'])) return $this->error('vendor_name is required.');

        $data = [
            'vendor_name' => sanitize_text_field($params['vendor_name']),
            'company_name' => isset($params['company_name']) ? sanitize_text_field($params['company_name']) : '',
            'service_type' => isset($params['service_type']) ? sanitize_text_field($params['service_type']) : 'General',
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s']);
        return $this->success('Vendor registered successfully', array_merge(['id' => $id], $data), 201);
    }
}
