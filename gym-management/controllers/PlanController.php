<?php
namespace GymErpApi\Controllers;
use GymErpApi\Repositories\PlanRepository;
use WP_REST_Request;

class PlanController extends BaseController {
    private $repo;
    public function __construct() { $this->repo = new PlanRepository(); }

    public function getPlans(WP_REST_Request $request) {
        return $this->success('Plans.', $this->repo->findAll($request->get_params(), ['name'], ['name']));
    }
    public function createPlan(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['name']) || empty($p['duration_days'])) return $this->error('Name and Duration required.');
        $id = $this->repo->create([
            'name' => $p['name'], 'duration_days' => $p['duration_days'],
            'price' => $p['price'] ?? 0, 'description' => $p['description'] ?? ''
        ], ['%s','%d','%f','%s']);
        return $id ? $this->success('Created.', ['id' => $id]) : $this->error('Failed.');
    }
}
