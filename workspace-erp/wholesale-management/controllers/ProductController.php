<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\ProductRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class ProductController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new ProductRepository();
    }

    public function getProducts(WP_REST_Request $request) {
        $searchable = ['sku', 'barcode', 'product_name', 'category', 'brand', 'hsn_code'];
        $sortable = ['id', 'sku', 'product_name', 'purchase_price', 'mrp', 'selling_price', 'created_at'];
        return $this->success('Products list.', $this->repo->findAll($request->get_params(), $searchable, $sortable));
    }

    public function getProduct(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        return $item ? $this->success('Product details.', $item) : $this->error('Product not found.', [], 404);
    }

    public function createProduct(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['product_name']) || empty($p['sku'])) {
            return $this->error('Product name and SKU are required.');
        }
        $data = [
            'sku'            => $p['sku'],
            'barcode'        => $p['barcode'] ?? '',
            'product_name'   => $p['product_name'],
            'category'       => $p['category'] ?? '',
            'brand'          => $p['brand'] ?? '',
            'unit'           => $p['unit'] ?? 'PCS',
            'purchase_price' => isset($p['purchase_price']) ? (float)$p['purchase_price'] : 0.00,
            'mrp'            => isset($p['mrp']) ? (float)$p['mrp'] : 0.00,
            'selling_price'  => isset($p['selling_price']) ? (float)$p['selling_price'] : 0.00,
            'gst_percentage' => isset($p['gst_percentage']) ? (float)$p['gst_percentage'] : 0.00,
            'hsn_code'       => $p['hsn_code'] ?? '',
            'status'         => $p['status'] ?? 'Active',
        ];
        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%f', '%f', '%s', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Product created.', ['id' => $id]) : $this->error('Failed to create product.');
    }

    public function updateProduct(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'sku'            => '%s',
            'barcode'        => '%s',
            'product_name'   => '%s',
            'category'       => '%s',
            'brand'          => '%s',
            'unit'           => '%s',
            'purchase_price' => '%f',
            'mrp'            => '%f',
            'selling_price'  => '%f',
            'gst_percentage' => '%f',
            'hsn_code'       => '%s',
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
        return $this->repo->update($id, $data, $formats) ? $this->success('Product updated.') : $this->error('Failed to update product.');
    }

    public function deleteProduct(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Product deleted.') : $this->error('Failed to delete product.');
    }
}
