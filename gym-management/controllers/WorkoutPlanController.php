<?php
namespace GymErpApi\Controllers;
use GymErpApi\Repositories\WorkoutPlanRepository;
use WP_REST_Request;

class WorkoutPlanController extends BaseController {
    private $repo;
    public function __construct() { $this->repo = new WorkoutPlanRepository(); }

    public function getWorkoutPlans(WP_REST_Request $request) {
        return $this->success('Workout plans.', $this->repo->findAllWithNames($request->get_params()));
    }

    public function getWorkoutPlan(WP_REST_Request $request) {
        $item = $this->repo->findById((int)$request['id']);
        return $item ? $this->success('Workout plan details.', $item) : $this->error('Not found.', [], 404);
    }

    public function createWorkoutPlan(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['member_id']) || empty($p['title'])) return $this->error('Member and Title required.');

        $data = [
            'member_id'   => (int)$p['member_id'],
            'trainer_id'  => !empty($p['trainer_id']) ? (int)$p['trainer_id'] : null,
            'title'       => sanitize_text_field($p['title']),
            'goal'        => sanitize_text_field($p['goal'] ?? 'General Fitness'),
            'level'       => sanitize_text_field($p['level'] ?? 'Beginner'),
            'monday'      => sanitize_textarea_field($p['monday'] ?? ''),
            'tuesday'     => sanitize_textarea_field($p['tuesday'] ?? ''),
            'wednesday'   => sanitize_textarea_field($p['wednesday'] ?? ''),
            'thursday'    => sanitize_textarea_field($p['thursday'] ?? ''),
            'friday'      => sanitize_textarea_field($p['friday'] ?? ''),
            'saturday'    => sanitize_textarea_field($p['saturday'] ?? ''),
            'sunday'      => sanitize_textarea_field($p['sunday'] ?? ''),
            'notes'       => sanitize_textarea_field($p['notes'] ?? ''),
            'start_date'  => $p['start_date'] ?? current_time('Y-m-d'),
            'end_date'    => $p['end_date'] ?? null,
            'status'      => 'Active'
        ];
        $formats = ['%d','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'];

        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Workout plan created.', ['id' => $id], 201) : $this->error('Failed to create.');
    }

    public function updateWorkoutPlan(WP_REST_Request $request) {
        $p = $request->get_json_params();
        $id = (int)$request['id'];
        if (!$this->repo->findById($id)) return $this->error('Not found.', [], 404);

        $data = [];
        $formats = [];
        $fields = ['title','goal','level','monday','tuesday','wednesday','thursday','friday','saturday','sunday','notes','start_date','end_date','status'];
        foreach ($fields as $f) {
            if (isset($p[$f])) { $data[$f] = $p[$f]; $formats[] = '%s'; }
        }
        if (isset($p['member_id'])) { $data['member_id'] = (int)$p['member_id']; $formats[] = '%d'; }
        if (isset($p['trainer_id'])) { $data['trainer_id'] = (int)$p['trainer_id']; $formats[] = '%d'; }

        return $this->repo->update($id, $data, $formats) ? $this->success('Updated.') : $this->error('Update failed.');
    }

    public function deleteWorkoutPlan(WP_REST_Request $request) {
        return $this->repo->delete((int)$request['id']) ? $this->success('Deleted.') : $this->error('Delete failed.');
    }
}
