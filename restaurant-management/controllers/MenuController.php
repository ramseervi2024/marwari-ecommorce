<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\MenuRepository;
use WP_REST_Request;

class MenuController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new MenuRepository();
    }

    public function getMenu(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $menu = $this->repository->all($limit, $offset);
        return $this->success('Menu items retrieved successfully.', $menu);
    }

    public function getMenuItem(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repository->find($id);
        if (!$item) {
            return $this->error('Menu item not found.', [], 404);
        }
        return $this->success('Menu item retrieved successfully.', $item);
    }

    public function createMenuItem(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['item_name']) || empty($params['item_code']) || empty($params['category_id']) || !isset($params['price'])) {
            return $this->error('Validation failed: item_name, item_code, category_id, and price are required.');
        }

        // Check unique code
        if ($this->repository->findByCode($params['item_code'])) {
            return $this->error('Item code already exists.');
        }

        $data = [
            'item_code' => sanitize_text_field($params['item_code']),
            'item_name' => sanitize_text_field($params['item_name']),
            'category_id' => intval($params['category_id']),
            'description' => sanitize_textarea_field($params['description'] ?? ''),
            'price' => floatval($params['price']),
            'cost_price' => floatval($params['cost_price'] ?? 0),
            'tax_percentage' => floatval($params['tax_percentage'] ?? 5),
            'preparation_time' => intval($params['preparation_time'] ?? 15),
            'image' => sanitize_text_field($params['image'] ?? ''),
            'status' => sanitize_text_field($params['status'] ?? 'Available')
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to create menu item.');
        }

        $data['id'] = $id;
        return $this->success('Menu item created successfully.', $data, 201);
    }

    public function updateMenuItem(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $params = $request->get_json_params();

        $item = $this->repository->find($id);
        if (!$item) {
            return $this->error('Menu item not found.', [], 404);
        }

        $data = [];
        if (isset($params['item_code'])) {
            $existing = $this->repository->findByCode($params['item_code']);
            if ($existing && intval($existing['id']) !== $id) {
                return $this->error('Item code already in use.');
            }
            $data['item_code'] = sanitize_text_field($params['item_code']);
        }
        if (isset($params['item_name'])) $data['item_name'] = sanitize_text_field($params['item_name']);
        if (isset($params['category_id'])) $data['category_id'] = intval($params['category_id']);
        if (isset($params['description'])) $data['description'] = sanitize_textarea_field($params['description']);
        if (isset($params['price'])) $data['price'] = floatval($params['price']);
        if (isset($params['cost_price'])) $data['cost_price'] = floatval($params['cost_price']);
        if (isset($params['tax_percentage'])) $data['tax_percentage'] = floatval($params['tax_percentage']);
        if (isset($params['preparation_time'])) $data['preparation_time'] = intval($params['preparation_time']);
        if (isset($params['image'])) $data['image'] = sanitize_text_field($params['image']);
        if (isset($params['status'])) $data['status'] = sanitize_text_field($params['status']);

        if (empty($data)) {
            return $this->error('No fields provided to update.');
        }

        if (!$this->repository->update($id, $data)) {
            return $this->error('Failed to update menu item.');
        }

        return $this->success('Menu item updated successfully.', array_merge($item, $data));
    }

    public function deleteMenuItem(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repository->find($id);
        if (!$item) {
            return $this->error('Menu item not found.', [], 404);
        }

        if (!$this->repository->delete($id)) {
            return $this->error('Failed to delete menu item.');
        }

        return $this->success('Menu item deleted successfully.');
    }
}
