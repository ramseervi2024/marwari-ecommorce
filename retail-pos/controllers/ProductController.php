<?php
namespace RetailPosApi\Controllers;

use RetailPosApi\Repositories\ProductRepository;
use RetailPosApi\Services\AuthService;
use WP_REST_Request;

class ProductController extends BaseController {
    private $productRepository;

    public function __construct() {
        $this->productRepository = new ProductRepository();
    }

    /**
     * GET /products
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'sku', 'barcode', 'product_name', 'selling_price', 'stock_quantity', 'status'];
        $search_fields = ['sku', 'barcode', 'product_name', 'unit'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['category_id'])) {
            $extra_filters['category_id'] = intval($params['category_id']);
        }
        if (isset($params['brand_id'])) {
            $extra_filters['brand_id'] = intval($params['brand_id']);
        }

        $results = $this->productRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        // Resolve category and brand names for display
        global $wpdb;
        $items = $results['data'];
        foreach ($items as &$item) {
            if ($item['category_id']) {
                $item['category_name'] = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}pos_categories WHERE id = %d", $item['category_id'])) ?: '';
            } else {
                $item['category_name'] = '';
            }
            if ($item['brand_id']) {
                $item['brand_name'] = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}pos_brands WHERE id = %d", $item['brand_id'])) ?: '';
            } else {
                $item['brand_name'] = '';
            }
        }
        $results['data'] = $items;

        return $this->success('Products retrieved successfully.', $results);
    }

    /**
     * GET /products/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return $this->error('Product not found.', [], 404);
        }

        global $wpdb;
        if ($product['category_id']) {
            $product['category_name'] = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}pos_categories WHERE id = %d", $product['category_id'])) ?: '';
        }
        if ($product['brand_id']) {
            $product['brand_name'] = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}pos_brands WHERE id = %d", $product['brand_id'])) ?: '';
        }

        return $this->success('Product retrieved successfully.', $product);
    }

    /**
     * POST /products
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['product_name'])) {
            return $this->error('Validation failed: product_name is required.');
        }

        // Auto-generate unique SKU if empty
        $sku = !empty($params['sku']) ? sanitize_text_field($params['sku']) : 'SKU-' . strtoupper(wp_generate_password(8, false, false));
        while ($this->productRepository->existsSku($sku)) {
            $sku = 'SKU-' . strtoupper(wp_generate_password(8, false, false));
        }

        // Auto-generate barcode if empty
        $barcode = !empty($params['barcode']) ? sanitize_text_field($params['barcode']) : '890' . sprintf('%010d', rand(1000000000, 9999999999));
        while ($this->productRepository->existsBarcode($barcode)) {
            $barcode = '890' . sprintf('%010d', rand(1000000000, 9999999999));
        }

        $data = [
            'sku' => $sku,
            'barcode' => $barcode,
            'product_name' => sanitize_text_field($params['product_name']),
            'category_id' => !empty($params['category_id']) ? intval($params['category_id']) : null,
            'brand_id' => !empty($params['brand_id']) ? intval($params['brand_id']) : null,
            'purchase_price' => floatval($params['purchase_price'] ?? 0.00),
            'selling_price' => floatval($params['selling_price'] ?? 0.00),
            'gst_percentage' => floatval($params['gst_percentage'] ?? 18.00),
            'stock_quantity' => floatval($params['stock_quantity'] ?? 0.00),
            'minimum_stock' => floatval($params['minimum_stock'] ?? 5.00),
            'unit' => sanitize_text_field($params['unit'] ?? 'PCS'),
            'image' => sanitize_url($params['image'] ?? ''),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%d', '%d', '%f', '%f', '%f', '%f', '%f', '%s', '%s', '%s'];
        $inserted_id = $this->productRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create product record.');
        }

        // Sync to inventory level table
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'pos_inventory', [
            'product_id' => $inserted_id,
            'available_stock' => $data['stock_quantity'],
            'minimum_stock' => $data['minimum_stock'],
            'reorder_level' => $data['minimum_stock'] * 2
        ], ['%d', '%f', '%f', '%f']);

        AuthService::logActivity(get_current_user_id(), 'PRODUCT_CREATE', "Created product SKU: $sku ID: $inserted_id");

        return $this->success('Product created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /products/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return $this->error('Product not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $string_fields = ['product_name', 'unit', 'image', 'status'];
        foreach ($string_fields as $field) {
            if (isset($params[$field])) {
                $data[$field] = ($field === 'image') ? sanitize_url($params[$field]) : sanitize_text_field($params[$field]);
                $formats[] = '%s';
            }
        }

        $numeric_fields = ['category_id', 'brand_id'];
        foreach ($numeric_fields as $field) {
            if (isset($params[$field])) {
                $data[$field] = intval($params[$field]);
                $formats[] = '%d';
            }
        }

        $float_fields = ['purchase_price', 'selling_price', 'gst_percentage', 'stock_quantity', 'minimum_stock'];
        foreach ($float_fields as $field) {
            if (isset($params[$field])) {
                $data[$field] = floatval($params[$field]);
                $formats[] = '%f';
            }
        }

        if (isset($params['sku'])) {
            $sku = sanitize_text_field($params['sku']);
            if ($sku !== $product['sku'] && $this->productRepository->existsSku($sku, $id)) {
                return $this->error('Duplicate SKU check failed: SKU is already assigned to another product.');
            }
            $data['sku'] = $sku;
            $formats[] = '%s';
        }

        if (isset($params['barcode'])) {
            $barcode = sanitize_text_field($params['barcode']);
            if ($barcode !== $product['barcode'] && $this->productRepository->existsBarcode($barcode, $id)) {
                return $this->error('Duplicate barcode check failed: barcode is already assigned to another product.');
            }
            $data['barcode'] = $barcode;
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->productRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update product.');
        }

        // Sync stock to inventory level table
        if (isset($data['stock_quantity']) || isset($data['minimum_stock'])) {
            global $wpdb;
            $inv_data = [];
            $inv_formats = [];
            if (isset($data['stock_quantity'])) {
                $inv_data['available_stock'] = $data['stock_quantity'];
                $inv_formats[] = '%f';
            }
            if (isset($data['minimum_stock'])) {
                $inv_data['minimum_stock'] = $data['minimum_stock'];
                $inv_data['reorder_level'] = $data['minimum_stock'] * 2;
                $inv_formats[] = '%f';
                $inv_formats[] = '%f';
            }
            $wpdb->update($wpdb->prefix . 'pos_inventory', $inv_data, ['product_id' => $id], $inv_formats, ['%d']);
        }

        AuthService::logActivity(get_current_user_id(), 'PRODUCT_UPDATE', "Updated product record ID: $id");

        return $this->success('Product updated successfully.', $this->productRepository->findById($id));
    }

    /**
     * DELETE /products/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return $this->error('Product not found.', [], 404);
        }

        $deleted = $this->productRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete product.');
        }

        // Also soft delete in inventory
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'pos_inventory',
            ['deleted_at' => current_time('mysql')],
            ['product_id' => $id],
            ['%s'],
            ['%d']
        );

        AuthService::logActivity(get_current_user_id(), 'PRODUCT_DELETE', "Soft deleted product SKU: $product[sku] ID: $id");

        return $this->success('Product deleted successfully.');
    }

    /**
     * GET /barcode/:code (Lookup product by SKU or Barcode)
     */
    public function getByBarcode(WP_REST_Request $request) {
        global $wpdb;
        $code = sanitize_text_field($request->get_param('code'));

        $product = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}pos_products WHERE (barcode = %s OR sku = %s) AND deleted_at IS NULL LIMIT 1", $code, $code),
            ARRAY_A
        );

        if (!$product) {
            return $this->error('Product not found with this barcode/SKU.', [], 404);
        }

        if ($product['category_id']) {
            $product['category_name'] = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}pos_categories WHERE id = %d", $product['category_id'])) ?: '';
        }
        if ($product['brand_id']) {
            $product['brand_name'] = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}pos_brands WHERE id = %d", $product['brand_id'])) ?: '';
        }

        return $this->success('Barcode scan successful.', $product);
    }

    /**
     * POST /barcode/generate
     */
    public function generateBarcode(WP_REST_Request $request) {
        $params = $request->get_json_params();
        $code = sanitize_text_field($params['code'] ?? '');

        if (empty($code)) {
            return $this->error('Validation failed: code is required.');
        }

        $svg = self::getCode39Svg($code);

        return $this->success('Barcode generated successfully.', [
            'code' => $code,
            'svg' => $svg,
            'html' => 'data:image/svg+xml;base64,' . base64_encode($svg)
        ]);
    }

    /**
     * NATIVE CODE 39 BARCODE SVG GENERATOR
     */
    private static function getCode39Svg(string $text): string {
        $code39 = [
            '0' => '1010011011010', '1' => '1101001010110', '2' => '1011001010110', '3' => '1101100101010',
            '4' => '1010011010110', '5' => '1101001101010', '6' => '1011001101010', '7' => '1010010110110',
            '8' => '1101001011010', '9' => '1011001011010', 'A' => '1101010010110', 'B' => '1011010010110',
            'C' => '1101101001010', 'D' => '1010110010110', 'E' => '1101011001010', 'F' => '1011011001010',
            'G' => '1010100110110', 'H' => '1101010011010', 'I' => '1011010011010', 'J' => '1010110011010',
            'K' => '1101010100110', 'L' => '1011010100110', 'M' => '1101101010010', 'N' => '1010110100110',
            'O' => '1101011010010', 'P' => '1011011010010', 'Q' => '1010101100110', 'R' => '1101010110010',
            'S' => '1011010110010', 'T' => '1010110110010', 'U' => '1100101010110', 'V' => '1001101010110',
            'W' => '1100110101010', 'X' => '1001011010110', 'Y' => '1100101101010', 'Z' => '1001101101010',
            '-' => '1001010110110', '.' => '1100101011010', ' ' => '1001101011010', '*' => '1001011011010',
            '$' => '1001001001010', '/' => '1001001010010', '+' => '1001010010010', '%' => '1010010010010'
        ];

        $text = strtoupper($text);
        if (strpos($text, '*') === false) {
            $text = '*' . $text . '*';
        }
        
        $bars = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            if (isset($code39[$char])) {
                $bars .= $code39[$char] . '0';
            }
        }
        
        $width = strlen($bars) * 2;
        $height = 80;
        
        $svg = "<svg width=\"{$width}\" height=\"{$height}\" viewBox=\"0 0 {$width} {$height}\" xmlns=\"http://www.w3.org/2000/svg\">\n";
        $svg .= "  <g fill=\"#000000\">\n";
        
        $x = 0;
        for ($i = 0; $i < strlen($bars); $i++) {
            if ($bars[$i] === '1') {
                $svg .= "    <rect x=\"{$x}\" y=\"0\" width=\"2\" height=\"60\" />\n";
            }
            $x += 2;
        }
        
        $cleanText = str_replace('*', '', $text);
        $svg .= "  </g>\n";
        $svg .= "  <text x=\"" . ($width / 2) . "\" y=\"75\" font-family=\"monospace\" font-size=\"12\" text-anchor=\"middle\" fill=\"#111827\">{$cleanText}</text>\n";
        $svg .= "</svg>";
        
        return $svg;
    }
}
