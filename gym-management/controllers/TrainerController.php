<?php
namespace GymErpApi\Controllers;
use GymErpApi\Repositories\TrainerRepository;
use WP_REST_Request;

class TrainerController extends BaseController {
    private $repo;
    public function __construct() { $this->repo = new TrainerRepository(); }

    public function getTrainers(WP_REST_Request $request) {
        return $this->success('Trainers.', $this->repo->findAll($request->get_params(), ['name','specialization'], ['name']));
    }
    public function createTrainer(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['name'])) return $this->error('Name required.');
        $id = $this->repo->create([
            'name' => $p['name'], 'mobile' => $p['mobile'] ?? '', 'email' => $p['email'] ?? '',
            'specialization' => $p['specialization'] ?? '', 'salary' => $p['salary'] ?? 0,
            'join_date' => current_time('Y-m-d')
        ], ['%s','%s','%s','%s','%f','%s']);
        return $id ? $this->success('Created.', ['id' => $id]) : $this->error('Failed.');
    }
    public function deleteTrainer(WP_REST_Request $request) {
        return $this->repo->delete((int)$request->get_param('id')) ? $this->success('Deleted.') : $this->error('Failed.');
    }
}
