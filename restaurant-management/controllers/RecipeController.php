<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\RecipeRepository;
use WP_REST_Request;

class RecipeController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new RecipeRepository();
    }

    public function getRecipes(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $recipes = $this->repository->all($limit, $offset);
        return $this->success('Recipes retrieved successfully.', $recipes);
    }

    public function getMenuItemRecipe(WP_REST_Request $request) {
        $menu_item_id = intval($request->get_param('menu_item_id'));
        $recipe = $this->repository->findByMenuItem($menu_item_id);
        return $this->success('Menu item recipe ingredients retrieved successfully.', $recipe);
    }

    public function saveRecipe(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['menu_item_id']) || !isset($params['ingredients']) || !is_array($params['ingredients'])) {
            return $this->error('Validation failed: menu_item_id and ingredients array are required.');
        }

        $menu_item_id = intval($params['menu_item_id']);
        
        // Clear old settings
        $this->repository->deleteByMenuItem($menu_item_id);

        foreach ($params['ingredients'] as $ing) {
            $this->repository->create([
                'menu_item_id' => $menu_item_id,
                'ingredient_id' => intval($ing['ingredient_id']),
                'quantity_required' => floatval($ing['quantity_required'])
            ]);
        }

        return $this->success('Recipe ingredients updated successfully.', $this->repository->findByMenuItem($menu_item_id));
    }

    public function deleteRecipe(WP_REST_Request $request) {
        $menu_item_id = intval($request->get_param('menu_item_id'));
        if (!$this->repository->deleteByMenuItem($menu_item_id)) {
            return $this->error('Failed to clear recipe.');
        }
        return $this->success('Recipe cleared successfully.');
    }
}
