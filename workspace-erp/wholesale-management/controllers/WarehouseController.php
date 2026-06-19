<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\WarehouseRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class WarehouseController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new WarehouseRepository();
    }

    public function getWarehouses(WP_REST_Request $request) {
        $searchable = ['warehouse_name', 'warehouse_code', 'city', 'state', 'manager_name'];
        $sortable = ['id', 'warehouse_name', 'warehouse_code', 'city', 'created_at'];
        return $this->success('Warehouses list.', $this->repo->findAll($request->get_params(), $searchable, $sortable));
    }

    public function getWarehouse(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        return $item ? $this->success('Warehouse details.', $item) : $this->error('Warehouse not found.', [], 404);
    }

    public function createWarehouse(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['warehouse_name'])) {
            return $this->error('Warehouse name is required.');
        }
        $data = [
            'warehouse_name' => $p['warehouse_name'],
            'warehouse_code' => $p['warehouse_code'] ?? $this->repo->generateCode('WH-', 'warehouse_code'),
            'address'        => $p['address'] ?? '',
            'city'           => $p['city'] ?? '',
            'state'          => $p['state'] ?? '',
            'manager_name'   => $p['manager_name'] ?? '',
            'contact'        => $p['contact'] ?? '',
            'status'         => $p['status'] ?? 'Active',
        ];
        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Warehouse created.', ['id' => $id, 'warehouse_code' => $data['warehouse_code']]) : $this->error('Failed to create warehouse.');
    }

    public function updateWarehouse(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'warehouse_name' => '%s',
            'warehouse_code' => '%s',
            'address'        => '%s',
            'city'           => '%s',
            'state'          => '%s',
            'manager_name'   => '%s',
            'contact'        => '%s',
            'status'         => '%s',
        ];
        $data = [];
        $formats = [];
        foreach ($fields as $f => $fmt) {
            if (isset($p[$f])) {
                $data[$f] = $p[$f];
                $formats[] = $fmt;
            }
        }
        if (empty($data)) {
            return $this->error('No fields to update.');
        }
        return $this->repo->update($id, $data, $formats) ? $this->success('Warehouse updated.') : $this->error('Failed to update warehouse.');
    }

    public function deleteWarehouse(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Warehouse deleted.') : $this->error('Failed to delete warehouse.');
    }
}
