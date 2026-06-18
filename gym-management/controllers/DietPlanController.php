<?php
namespace GymErpApi\Controllers;
use GymErpApi\Repositories\DietPlanRepository;
use WP_REST_Request;

class DietPlanController extends BaseController {
    private $repo;
    public function __construct() { $this->repo = new DietPlanRepository(); }

    public function getDietPlans(WP_REST_Request $request) {
        return $this->success('Diet Plans.', $this->repo->findAll($request->get_params()));
    }
    public function assignDietPlan(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['member_id']) || empty($p['plan_details'])) return $this->error('Member and Plan Details required.');
        $id = $this->repo->create([
            'member_id' => $p['member_id'], 'trainer_id' => $p['trainer_id'] ?? null,
            'plan_details' => $p['plan_details'], 'assigned_date' => current_time('Y-m-d')
        ], ['%d','%d','%s','%s']);
        return $id ? $this->success('Diet plan assigned.', ['id' => $id]) : $this->error('Failed.');
    }
}
