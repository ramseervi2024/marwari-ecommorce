<?php
namespace GymErpApi\Repositories;
class WorkoutPlanRepository extends BaseRepository {
    public function __construct() { parent::__construct('workout_plans'); }

    public function findAllWithNames(array $args = []): array {
        global $wpdb;
        $p = $wpdb->prefix;
        $where = "w.deleted_at IS NULL";
        $page = isset($args['page']) ? max(1, (int)$args['page']) : 1;
        $limit = isset($args['limit']) ? max(1, (int)$args['limit']) : 20;
        $offset = ($page - 1) * $limit;

        if (!empty($args['search'])) {
            $s = '%' . $wpdb->esc_like($args['search']) . '%';
            $where .= $wpdb->prepare(" AND (w.title LIKE %s OR m.name LIKE %s)", $s, $s);
        }

        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$p}gym_workout_plans w LEFT JOIN {$p}gym_members m ON w.member_id=m.id WHERE $where");
        $data = $wpdb->get_results("SELECT w.*, m.name AS member_name, m.member_id AS member_code, t.name AS trainer_name
            FROM {$p}gym_workout_plans w
            LEFT JOIN {$p}gym_members m ON w.member_id = m.id
            LEFT JOIN {$p}gym_trainers t ON w.trainer_id = t.id
            WHERE $where ORDER BY w.id DESC LIMIT $limit OFFSET $offset", ARRAY_A);
        return ['data' => $data ?: [], 'total' => (int)$total, 'page' => $page, 'limit' => $limit, 'pages' => ceil($total / $limit)];
    }
}
