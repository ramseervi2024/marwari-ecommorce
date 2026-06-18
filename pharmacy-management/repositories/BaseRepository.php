<?php
namespace PharmacyErpApi\Repositories;

if (!defined('ABSPATH')) exit;

class BaseRepository {
    protected string $table_name;
    protected bool $has_soft_delete;

    public function __construct(string $suffix, bool $soft_delete = true) {
        global $wpdb;
        $this->table_name      = $wpdb->prefix . 'pharmacy_' . $suffix;
        $this->has_soft_delete = $soft_delete;
    }

    public function findById(int $id): ?array {
        global $wpdb;
        $q = "SELECT * FROM {$this->table_name} WHERE id=%d" . ($this->has_soft_delete ? " AND deleted_at IS NULL" : "");
        return $wpdb->get_row($wpdb->prepare($q, $id), ARRAY_A) ?: null;
    }

    public function create(array $data, array $formats): ?int {
        global $wpdb;
        $r = $wpdb->insert($this->table_name, $data, $formats);
        return $r !== false ? (int)$wpdb->insert_id : null;
    }

    public function update(int $id, array $data, array $formats): bool {
        global $wpdb;
        return $wpdb->update($this->table_name, $data, ['id' => $id], $formats, ['%d']) !== false;
    }

    public function delete(int $id): bool {
        global $wpdb;
        if ($this->has_soft_delete) {
            return $wpdb->update($this->table_name, ['deleted_at' => current_time('mysql')], ['id' => $id], ['%s'], ['%d']) !== false;
        }
        return $wpdb->delete($this->table_name, ['id' => $id], ['%d']) !== false;
    }

    public function findAll(array $params = [], array $allowed_sorts = [], array $search_fields = [], array $extra_filters = []): array {
        global $wpdb;
        $page   = max(1, (int)($params['page'] ?? 1));
        $limit  = max(1, (int)($params['limit'] ?? 15));
        $offset = ($page - 1) * $limit;
        $sort   = (isset($params['sort']) && in_array($params['sort'], $allowed_sorts)) ? $params['sort'] : 'id';
        $order  = (isset($params['order']) && strtoupper($params['order']) === 'ASC') ? 'ASC' : 'DESC';

        $where = $this->has_soft_delete ? ['deleted_at IS NULL'] : [];
        $args  = [];

        foreach ($extra_filters as $col => $val) {
            if ($val !== null && $val !== '') {
                $where[] = "$col = %s";
                $args[]  = $val;
            }
        }

        if (!empty($params['search']) && !empty($search_fields)) {
            $sv    = '%' . $wpdb->esc_like($params['search']) . '%';
            $conds = [];
            foreach ($search_fields as $f) { $conds[] = "$f LIKE %s"; $args[] = $sv; }
            $where[] = '(' . implode(' OR ', $conds) . ')';
        }

        $wc = !empty($where) ? implode(' AND ', $where) : '1=1';

        $total_q = "SELECT COUNT(*) FROM {$this->table_name} WHERE $wc";
        $total   = (int)($args ? $wpdb->get_var($wpdb->prepare($total_q, $args)) : $wpdb->get_var($total_q));

        $data_q = "SELECT * FROM {$this->table_name} WHERE $wc ORDER BY $sort $order LIMIT %d OFFSET %d";
        $rows   = $wpdb->get_results($wpdb->prepare($data_q, array_merge($args, [$limit, $offset])), ARRAY_A);

        return ['total' => $total, 'page' => $page, 'limit' => $limit, 'pages' => $total ? ceil($total / $limit) : 0, 'data' => $rows ?: []];
    }
}
