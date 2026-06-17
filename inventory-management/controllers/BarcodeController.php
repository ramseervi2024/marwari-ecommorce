<?php
namespace InventoryManagementApi\Controllers;

use InventoryManagementApi\Repositories\ProductRepository;
use WP_REST_Request;

class BarcodeController extends BaseController {
    private $productRepository;

    public function __construct() {
        $this->productRepository = new ProductRepository();
    }

    /**
     * POST /barcode/generate
     */
    public function generate(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['product_id'])) {
            return $this->error('product_id is required.');
        }

        $pid = intval($params['product_id']);
        $product = $this->productRepository->findById($pid);

        if (!$product) {
            return $this->error('Product not found.');
        }

        $barcode = $product['barcode'];
        if (empty($barcode)) {
            // Generate standard UPC/EAN mock code
            $barcode = '8901030' . sprintf('%06d', rand(100000, 999999));
            $this->productRepository->update($pid, ['barcode' => $barcode], ['%s']);
        }

        return $this->success('Barcode details retrieved.', [
            'product_id' => $pid,
            'sku' => $product['sku'],
            'product_name' => $product['product_name'],
            'barcode' => $barcode,
            'label_url' => "https://bwipjs-api.metafloor.com/?bcid=code128&text=" . urlencode($barcode) . "&scale=2&rotate=N&includetext"
        ]);
    }

    /**
     * GET /barcode/{code} (Lookup product by barcode)
     */
    public function lookup(WP_REST_Request $request) {
        $code = sanitize_text_field($request->get_param('code'));
        if (empty($code)) {
            return $this->error('Barcode is required.');
        }

        global $wpdb;
        $table = $wpdb->prefix . 'inv_products';
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE barcode = %s AND deleted_at IS NULL", $code),
            ARRAY_A
        );

        if (!$row) {
            return $this->error('No product found matching this barcode.', [], 404);
        }

        return $this->success('Product found.', $row);
    }
}
