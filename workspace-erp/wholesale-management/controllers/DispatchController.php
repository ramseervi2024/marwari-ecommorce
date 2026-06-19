<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\DispatchRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class DispatchController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new DispatchRepository();
    }

    public function getDispatches(WP_REST_Request $request) {
        $searchable = ['dispatch_number', 'vehicle_number', 'driver_name', 'status'];
        $sortable = ['id', 'dispatch_number', 'dispatch_date', 'expected_delivery_date', 'status', 'created_at'];
        return $this->success('Dispatches list.', $this->repo->findAll($request->get_params(), $searchable, $sortable));
    }

    public function getDispatch(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        return $item ? $this->success('Dispatch details.', $item) : $this->error('Dispatch not found.', [], 404);
    }

    public function createDispatch(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['order_id'])) {
            return $this->error('Order ID is required.');
        }
        $data = [
            'dispatch_number'        => $p['dispatch_number'] ?? $this->repo->generateCode('DISP-', 'dispatch_number'),
            'order_id'               => (int)$p['order_id'],
            'vehicle_number'         => $p['vehicle_number'] ?? '',
            'driver_name'            => $p['driver_name'] ?? '',
            'driver_mobile'          => $p['driver_mobile'] ?? '',
            'dispatch_date'          => $p['dispatch_date'] ?? null,
            'expected_delivery_date' => $p['expected_delivery_date'] ?? null,
            'actual_delivery_date'   => $p['actual_delivery_date'] ?? null,
            'status'                 => $p['status'] ?? 'Pending',
            'notes'                  => $p['notes'] ?? '',
        ];
        $formats = ['%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Dispatch created.', ['id' => $id, 'dispatch_number' => $data['dispatch_number']]) : $this->error('Failed to create dispatch.');
    }

    public function updateDispatch(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'order_id'               => '%d',
            'vehicle_number'         => '%s',
            'driver_name'            => '%s',
            'driver_mobile'          => '%s',
            'dispatch_date'          => '%s',
            'expected_delivery_date' => '%s',
            'actual_delivery_date'   => '%s',
            'status'                 => '%s',
            'notes'                  => '%s',
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
        return $this->repo->update($id, $data, $formats) ? $this->success('Dispatch updated.') : $this->error('Failed to update dispatch.');
    }

    public function deleteDispatch(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Dispatch deleted.') : $this->error('Failed to delete dispatch.');
    }
}
