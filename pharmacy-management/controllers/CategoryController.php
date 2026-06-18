<?php
namespace PharmacyErpApi\Controllers;

use PharmacyErpApi\Repositories\CategoryRepository;
use WP_REST_Request;

class CategoryController extends BaseController {
    private $catRepo;
    public function __construct() { $this->catRepo = new CategoryRepository(); }

    public function getCategories(WP_REST_Request $request) {
        $p = $request->get_params();
        $r = $this->catRepo->findAll($p, ['id','name'], ['name']);
        return $this->success('Categories retrieved.', $r);
    }
    public function createCategory(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['name'])) return $this->error('Name required.');
        $id = $this->catRepo->create(['name' => $p['name'], 'description' => $p['description'] ?? ''], ['%s', '%s']);
        return $id ? $this->success('Category created.', ['id' => $id]) : $this->error('Failed to create.');
    }
    public function updateCategory(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $data = []; $formats = [];
        if (isset($p['name'])) { $data['name'] = $p['name']; $formats[] = '%s'; }
        if (isset($p['description'])) { $data['description'] = $p['description']; $formats[] = '%s'; }
        if (empty($data)) return $this->error('No fields to update.');
        $ok = $this->catRepo->update($id, $data, $formats);
        return $ok ? $this->success('Category updated.') : $this->error('Failed to update.');
    }
    public function deleteCategory(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->catRepo->delete($id) ? $this->success('Category deleted.') : $this->error('Failed to delete.');
    }
}
