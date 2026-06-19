<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\PricingRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class PricingController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new PricingRepository();
    }

    public function getPricings(WP_REST_Request $request) {
        $searchable = ['dealer_category', 'scheme_name'];
        $sortable = ['id', 'product_id', 'special_price', 'effective_date', 'expiry_date'];
        return $this->success('Pricing rules.', $this->repo->findAll($request->get_params(), $searchable, $sortable));
    }

    public function getPricing(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        return $item ? $this->success('Pricing rule details.', $item) : $this->error('Pricing rule not found.', [], 404);
    }

    public function createPricing(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['product_id'])) {
            return $this->error('Product ID is required.');
        }
        $data = [
            'product_id'          => (int)$p['product_id'],
            'dealer_category'     => $p['dealer_category'] ?? '',
            'dealer_id'           => !empty($p['dealer_id']) ? (int)$p['dealer_id'] : null,
            'special_price'       => isset($p['special_price']) ? (float)$p['special_price'] : 0.00,
            'discount_percentage' => isset($p['discount_percentage']) ? (float)$p['discount_percentage'] : 0.00,
            'min_quantity'        => isset($p['min_quantity']) ? (int)$p['min_quantity'] : 1,
            'effective_date'      => $p['effective_date'] ?? null,
            'expiry_date'         => $p['expiry_date'] ?? null,
            'scheme_name'         => $p['scheme_name'] ?? '',
            'status'              => $p['status'] ?? 'Active',
        ];
        $formats = ['%d', '%s', '%d', '%f', '%f', '%d', '%s', '%s', '%s', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Pricing rule created.', ['id' => $id]) : $this->error('Failed to create pricing rule.');
    }

    public function updatePricing(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'product_id'          => '%d',
            'dealer_category'     => '%s',
            'dealer_id'           => '%d',
            'special_price'       => '%f',
            'discount_percentage' => '%f',
            'min_quantity'        => '%d',
            'effective_date'      => '%s',
            'expiry_date'         => '%s',
            'scheme_name'         => '%s',
            'status'              => '%s',
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
        return $this->repo->update($id, $data, $formats) ? $this->success('Pricing rule updated.') : $this->error('Failed to update pricing rule.');
    }

    public function deletePricing(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Pricing rule deleted.') : $this->error('Failed to delete pricing rule.');
    }
}
