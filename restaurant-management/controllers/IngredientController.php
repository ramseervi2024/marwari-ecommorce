<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\IngredientRepository;
use WP_REST_Request;

class IngredientController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new IngredientRepository();
    }

    public function getIngredients(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $ingredients = $this->repository->all($limit, $offset);
        return $this->success('Ingredients retrieved successfully.', $ingredients);
    }

    public function createIngredient(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['ingredient_name']) || empty($params['unit'])) {
            return $this->error('Validation failed: ingredient_name and unit are required.');
        }

        $data = [
            'ingredient_name' => sanitize_text_field($params['ingredient_name']),
            'unit' => sanitize_text_field($params['unit']),
            'current_stock' => floatval($params['current_stock'] ?? 0.00),
            'minimum_stock' => floatval($params['minimum_stock'] ?? 1.00),
            'purchase_price' => floatval($params['purchase_price'] ?? 0.00),
            'supplier_id' => !empty($params['supplier_id']) ? intval($params['supplier_id']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'Active')
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to register ingredient.');
        }

        $data['id'] = $id;
        return $this->success('Ingredient registered successfully.', $data, 201);
    }

    public function updateIngredient(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $params = $request->get_json_params();

        $ingredient = $this->repository->find($id);
        if (!$ingredient) {
            return $this->error('Ingredient not found.', [], 404);
        }

        $data = [];
        if (isset($params['ingredient_name'])) $data['ingredient_name'] = sanitize_text_field($params['ingredient_name']);
        if (isset($params['unit'])) $data['unit'] = sanitize_text_field($params['unit']);
        if (isset($params['current_stock'])) $data['current_stock'] = floatval($params['current_stock']);
        if (isset($params['minimum_stock'])) $data['minimum_stock'] = floatval($params['minimum_stock']);
        if (isset($params['purchase_price'])) $data['purchase_price'] = floatval($params['purchase_price']);
        if (isset($params['supplier_id'])) $data['supplier_id'] = intval($params['supplier_id']);
        if (isset($params['status'])) $data['status'] = sanitize_text_field($params['status']);

        if (empty($data)) {
            return $this->error('No fields provided to update.');
        }

        if (!$this->repository->update($id, $data)) {
            return $this->error('Failed to update ingredient details.');
        }

        return $this->success('Ingredient updated successfully.', array_merge($ingredient, $data));
    }

    public function deleteIngredient(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $ingredient = $this->repository->find($id);
        if (!$ingredient) {
            return $this->error('Ingredient not found.', [], 404);
        }

        if (!$this->repository->delete($id)) {
            return $this->error('Failed to delete ingredient.');
        }

        return $this->success('Ingredient deleted successfully.');
    }
}
