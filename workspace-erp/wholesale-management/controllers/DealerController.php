<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\DealerRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class DealerController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new DealerRepository();
    }

    public function getDealers(WP_REST_Request $request) {
        $searchable = ['dealer_code', 'dealer_name', 'owner_name', 'mobile', 'email', 'city', 'state'];
        $sortable = ['id', 'dealer_code', 'dealer_name', 'city', 'state', 'created_at'];
        return $this->success('Dealers list.', $this->repo->findAll($request->get_params(), $searchable, $sortable));
    }

    public function getDealer(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        return $item ? $this->success('Dealer details.', $item) : $this->error('Dealer not found.', [], 404);
    }

    public function createDealer(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['dealer_name'])) {
            return $this->error('Dealer name is required.');
        }
        $data = [
            'dealer_code'      => $p['dealer_code'] ?? $this->repo->generateCode('DLR-', 'dealer_code'),
            'dealer_name'      => $p['dealer_name'],
            'owner_name'       => $p['owner_name'] ?? '',
            'mobile'           => $p['mobile'] ?? '',
            'email'            => $p['email'] ?? '',
            'gst_number'       => $p['gst_number'] ?? '',
            'address'          => $p['address'] ?? '',
            'city'             => $p['city'] ?? '',
            'state'            => $p['state'] ?? '',
            'pincode'          => $p['pincode'] ?? '',
            'credit_limit'     => isset($p['credit_limit']) ? (float)$p['credit_limit'] : 0.00,
            'available_credit' => isset($p['credit_limit']) ? (float)$p['credit_limit'] : 0.00,
            'status'           => $p['status'] ?? 'Active',
            'notes'            => $p['notes'] ?? '',
        ];
        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Dealer created.', ['id' => $id, 'dealer_code' => $data['dealer_code']]) : $this->error('Failed to create dealer.');
    }

    public function updateDealer(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'dealer_name'      => '%s',
            'owner_name'       => '%s',
            'mobile'           => '%s',
            'email'            => '%s',
            'gst_number'       => '%s',
            'address'          => '%s',
            'city'             => '%s',
            'state'            => '%s',
            'pincode'          => '%s',
            'credit_limit'     => '%f',
            'available_credit' => '%f',
            'status'           => '%s',
            'notes'            => '%s',
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
        return $this->repo->update($id, $data, $formats) ? $this->success('Dealer updated.') : $this->error('Failed to update dealer.');
    }

    public function deleteDealer(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Dealer deleted.') : $this->error('Failed to delete dealer.');
    }
}
