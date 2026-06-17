<?php
namespace InventoryManagementApi\Controllers;

use InventoryManagementApi\Repositories\ProductRepository;
use InventoryManagementApi\Services\AuthService;
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
        $allowed_sorts = ['id', 'sku', 'product_name', 'selling_price', 'created_at'];
        $search_fields = ['sku', 'barcode', 'product_name', 'category', 'brand'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['category'])) {
            $extra_filters['category'] = sanitize_text_field($params['category']);
        }

        $results = $this->productRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Products list retrieved successfully.', $results);
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

        return $this->success('Product retrieved successfully.', $product);
    }

    /**
     * POST /products
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['product_name']) || empty($params['sku'])) {
            return $this->error('Validation failed: product_name and sku are required.');
        }

        if ($this->productRepository->existsSku($params['sku'])) {
            return $this->error('Product SKU already exists.');
        }

        if (!empty($params['barcode']) && $this->productRepository->existsBarcode($params['barcode'])) {
            return $this->error('Barcode already exists.');
        }

        $data = [
            'sku' => sanitize_text_field($params['sku']),
            'barcode' => sanitize_text_field($params['barcode'] ?? ''),
            'product_name' => sanitize_text_field($params['product_name']),
            'description' => sanitize_textarea_field($params['description'] ?? ''),
            'category' => sanitize_text_field($params['category'] ?? 'General'),
            'brand' => sanitize_text_field($params['brand'] ?? ''),
            'unit' => sanitize_text_field($params['unit'] ?? 'PCS'),
            'purchase_price' => floatval($params['purchase_price'] ?? 0.00),
            'selling_price' => floatval($params['selling_price'] ?? 0.00),
            'minimum_stock' => intval($params['minimum_stock'] ?? 10),
            'maximum_stock' => intval($params['maximum_stock'] ?? 1000),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%d', '%d', '%s'];
        $inserted_id = $this->productRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create product.');
        }

        AuthService::logActivity(get_current_user_id(), 'PRODUCT_CREATE', "Created product {$data['sku']} - {$data['product_name']}");

        return $this->success('Product profile created successfully.', array_merge(['id' => $inserted_id], $data), 201);
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

        if (isset($params['sku'])) {
            if ($this->productRepository->existsSku($params['sku'], $id)) {
                return $this->error('SKU already in use.');
            }
            $data['sku'] = sanitize_text_field($params['sku']);
            $formats[] = '%s';
        }

        if (isset($params['barcode']) && !empty($params['barcode'])) {
            if ($this->productRepository->existsBarcode($params['barcode'], $id)) {
                return $this->error('Barcode already in use.');
            }
            $data['barcode'] = sanitize_text_field($params['barcode']);
            $formats[] = '%s';
        }

        $fields = [
            'product_name' => '%s',
            'description' => '%s',
            'category' => '%s',
            'brand' => '%s',
            'unit' => '%s',
            'purchase_price' => '%f',
            'selling_price' => '%f',
            'minimum_stock' => '%d',
            'maximum_stock' => '%d',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%f') {
                    $data[$field] = floatval($params[$field]);
                } elseif ($format === '%d') {
                    $data[$field] = intval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->productRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update product details.');
        }

        AuthService::logActivity(get_current_user_id(), 'PRODUCT_UPDATE', "Updated product ID: $id");

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

        AuthService::logActivity(get_current_user_id(), 'PRODUCT_DELETE', "Soft deleted product ID: $id ({$product['sku']})");

        return $this->success('Product deleted successfully.');
    }
}
