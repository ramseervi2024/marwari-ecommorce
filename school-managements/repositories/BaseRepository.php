<?php
namespace SchoolManagementApi\Repositories;

class BaseRepository {
    protected $table_name;

    public function __construct(string $table_suffix) {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'school_' . $table_suffix;
    }

    /**
     * Find a single record by ID
     */
    public function findById(int $id): ?array {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d AND deleted_at IS NULL", $id),
            ARRAY_A
        );
        return $row ?: null;
    }

    /**
     * Create a new record
     */
    public function create(array $data, array $formats): ?int {
        global $wpdb;
        $result = $wpdb->insert($this->table_name, $data, $formats);
        return $result !== false ? (int)$wpdb->insert_id : null;
    }

    /**
     * Update an existing record
     */
    public function update(int $id, array $data, array $formats): bool {
        global $wpdb;
        $result = $wpdb->update(
            $this->table_name,
            $data,
            ['id' => $id],
            $formats,
            ['%d']
        );
        return $result !== false;
    }

    /**
     * Soft delete a record
     */
    public function delete(int $id): bool {
        global $wpdb;
        $result = $wpdb->update(
            $this->table_name,
            ['deleted_at' => current_time('mysql')],
            ['id' => $id],
            ['%s'],
            ['%d']
        );
        return $result !== false;
    }

    /**
     * Check if a field value exists (excluding active record ID)
     */
    public function exists(string $column, string $value, ?int $exclude_id = null): bool {
        global $wpdb;
        $query = "SELECT COUNT(*) FROM {$this->table_name} WHERE {$column} = %s AND deleted_at IS NULL";
        $args = [$value];
        if ($exclude_id !== null) {
            $query .= " AND id != %d";
            $args[] = $exclude_id;
        }
        return (int)$wpdb->get_var($wpdb->prepare($query, $args)) > 0;
    }

    /**
     * Find all records with filters, search, pagination, and sorting
     */
    public function findAll(array $params = [], array $allowed_sorts = [], array $search_fields = [], array $extra_filters = []): array {
        global $wpdb;
        
        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $sort = isset($params['sort']) && in_array($params['sort'], $allowed_sorts) ? $params['sort'] : 'id';
        $order = isset($params['order']) && strtoupper($params['order']) === 'DESC' ? 'DESC' : 'ASC';

        $where = ["deleted_at IS NULL"];
        $args = [];

        // Apply extra specific column filters
        foreach ($extra_filters as $col => $val) {
            if ($val !== null && $val !== '') {
                $where[] = "$col = %s";
                $args[] = $val;
            }
        }

        // Apply search query
        if (!empty($params['search']) && !empty($search_fields)) {
            $search_val = '%' . $wpdb->esc_like($params['search']) . '%';
            $search_conds = [];
            foreach ($search_fields as $field) {
                $search_conds[] = "$field LIKE %s";
                $args[] = $search_val;
            }
            $where[] = '(' . implode(' OR ', $search_conds) . ')';
        }

        $where_clause = implode(" AND ", $where);

        // Get count
        $total_query = "SELECT COUNT(*) FROM {$this->table_name} WHERE $where_clause";
        if (!empty($args)) {
            $total_count = (int)$wpdb->get_var($wpdb->prepare($total_query, $args));
        } else {
            $total_count = (int)$wpdb->get_var($total_query);
        }

        // Get results
        $data_query = "SELECT * FROM {$this->table_name} WHERE $where_clause ORDER BY $sort $order LIMIT %d OFFSET %d";
        $data_args = array_merge($args, [$limit, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($data_query, $data_args), ARRAY_A);

        return [
            'total' => $total_count,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total_count / $limit),
            'data' => $rows ?: []
        ];
    }
}
