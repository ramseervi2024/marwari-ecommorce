<?php
namespace GymErpApi\Repositories;
abstract class BaseRepository {
    protected $table_name;
    protected $has_soft_delete = true;
    public function __construct(string $table, bool $has_soft_delete = true) {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'gym_' . $table;
        $this->has_soft_delete = $has_soft_delete;
    }
    public function findAll(array $args = [], array $search_cols = [], array $order_cols = ['id']): array {
        global $wpdb;
        $where = $this->has_soft_delete ? ["deleted_at IS NULL"] : ["1=1"];
        if (!empty($args['search']) && !empty($search_cols)) {
            $s = '%' . $wpdb->esc_like($args['search']) . '%';
            $clauses = [];
            foreach ($search_cols as $c) $clauses[] = $wpdb->prepare("$c LIKE %s", $s);
            $where[] = '(' . implode(' OR ', $clauses) . ')';
        }
        $where_sql = implode(' AND ', $where);
        $order_by = !empty($args['orderby']) && in_array($args['orderby'], $order_cols) ? $args['orderby'] : 'id';
        $order = !empty($args['order']) && strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';
        $page = isset($args['page']) ? max(1, (int)$args['page']) : 1;
        $limit = isset($args['limit']) ? max(1, (int)$args['limit']) : 10;
        $offset = ($page - 1) * $limit;
        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE $where_sql");
        $data = $wpdb->get_results("SELECT * FROM {$this->table_name} WHERE $where_sql ORDER BY $order_by $order LIMIT $limit OFFSET $offset", ARRAY_A);
        return ['data' => $data ?: [], 'total' => (int)$total, 'page' => $page, 'limit' => $limit, 'pages' => ceil($total / $limit)];
    }
    public function findById(int $id): ?array {
        global $wpdb;
        $where = $this->has_soft_delete ? " AND deleted_at IS NULL" : "";
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d $where", $id), ARRAY_A) ?: null;
    }
    public function create(array $data, array $formats): ?int {
        global $wpdb;
        $wpdb->insert($this->table_name, $data, $formats);
        return $wpdb->insert_id ?: null;
    }
    public function update(int $id, array $data, array $formats): bool {
        global $wpdb;
        return $wpdb->update($this->table_name, $data, ['id' => $id], $formats, ['%d']) !== false;
    }
    public function delete(int $id): bool {
        global $wpdb;
        if ($this->has_soft_delete) return $wpdb->update($this->table_name, ['deleted_at' => current_time('mysql')], ['id' => $id]) !== false;
        return $wpdb->delete($this->table_name, ['id' => $id]) !== false;
    }
}
