<?php
namespace InventoryManagementApi\Controllers;

use InventoryManagementApi\Repositories\ProductRepository;
use WP_REST_Request;

class QrcodeController extends BaseController {
    private $productRepository;

    public function __construct() {
        $this->productRepository = new ProductRepository();
    }

    /**
     * POST /qrcode/generate
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

        $qr_data = 'INV-SKU-' . $product['sku'];

        return $this->success('QR Code details generated.', [
            'product_id' => $pid,
            'sku' => $product['sku'],
            'product_name' => $product['product_name'],
            'qr_data' => $qr_data,
            'label_url' => "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_data)
        ]);
    }

    /**
     * GET /qrcode/{code}
     */
    public function lookup(WP_REST_Request $request) {
        $code = sanitize_text_field($request->get_param('code'));
        if (empty($code)) {
            return $this->error('QR code data is required.');
        }

        // Parse SKU from code: 'INV-SKU-<SKU>'
        $sku = str_replace('INV-SKU-', '', $code);

        global $wpdb;
        $table = $wpdb->prefix . 'inv_products';
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE sku = %s AND deleted_at IS NULL", $sku),
            ARRAY_A
        );

        if (!$row) {
            return $this->error('No product found matching this QR code.', [], 404);
        }

        return $this->success('Product found.', $row);
    }
}
