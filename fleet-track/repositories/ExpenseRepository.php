<?php
namespace FleetTrackPro\Repositories;

class ExpenseRepository extends BaseRepository {
    
    public function __construct() {
        parent::__construct('expenses');
    }

    /**
     * Get expense row with descriptive metadata
     */
    public function findExpenseWithDetails(int $id): ?array {
        global $wpdb;
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';
        $table_drivers = $wpdb->prefix . 'fleet_drivers';
        
        $query = "SELECT e.*, 
                  v.vehicle_number, v.vehicle_type,
                  d.name as driver_name
                  FROM {$this->table_name} e
                  LEFT JOIN $table_vehicles v ON e.vehicle_id = v.id
                  LEFT JOIN $table_drivers d ON e.driver_id = d.id
                  WHERE e.id = %d AND e.deleted_at IS NULL";
                  
        $row = $wpdb->get_row($wpdb->prepare($query, $id), ARRAY_A);
        return $row ?: null;
    }

    /**
     * Get all expenses with details
     */
    public function findAllWithDetails(array $params = []): array {
        global $wpdb;
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';
        $table_drivers = $wpdb->prefix . 'fleet_drivers';

        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $allowed_sorts = ['id', 'expense_date', 'amount', 'expense_type'];
        $sort = isset($params['sort']) && in_array($params['sort'], $allowed_sorts) ? 'e.' . $params['sort'] : 'e.id';
        $order = isset($params['order']) && strtoupper($params['order']) === 'DESC' ? 'DESC' : 'ASC';

        $where = ["e.deleted_at IS NULL"];
        $args = [];

        // Apply filters
        if (!empty($params['vehicle_id'])) {
            $where[] = "e.vehicle_id = %d";
            $args[] = (int)$params['vehicle_id'];
        }
        if (!empty($params['driver_id'])) {
            $where[] = "e.driver_id = %d";
            $args[] = (int)$params['driver_id'];
        }
        if (!empty($params['trip_id'])) {
            $where[] = "e.trip_id = %d";
            $args[] = (int)$params['trip_id'];
        }
        if (!empty($params['expense_type'])) {
            $where[] = "e.expense_type = %s";
            $args[] = sanitize_text_field($params['expense_type']);
        }

        // Search text matching
        if (!empty($params['search'])) {
            $search_val = '%' . $wpdb->esc_like($params['search']) . '%';
            $where[] = "(v.vehicle_number LIKE %s OR d.name LIKE %s OR e.description LIKE %s OR e.expense_type LIKE %s)";
            $args[] = $search_val;
            $args[] = $search_val;
            $args[] = $search_val;
            $args[] = $search_val;
        }

        $where_clause = implode(" AND ", $where);

        // Get count
        $total_query = "SELECT COUNT(*) FROM {$this->table_name} e
                        LEFT JOIN $table_vehicles v ON e.vehicle_id = v.id
                        LEFT JOIN $table_drivers d ON e.driver_id = d.id
                        WHERE $where_clause";
        if (!empty($args)) {
            $total_count = (int)$wpdb->get_var($wpdb->prepare($total_query, $args));
        } else {
            $total_count = (int)$wpdb->get_var($total_query);
        }

        // Get rows
        $data_query = "SELECT e.*, 
                      v.vehicle_number, v.vehicle_type,
                      d.name as driver_name
                      FROM {$this->table_name} e
                      LEFT JOIN $table_vehicles v ON e.vehicle_id = v.id
                      LEFT JOIN $table_drivers d ON e.driver_id = d.id
                      WHERE $where_clause 
                      ORDER BY $sort $order 
                      LIMIT %d OFFSET %d";
                      
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
