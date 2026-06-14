<?php
namespace FleetTrackPro\Repositories;

class FuelRepository extends BaseRepository {
    
    public function __construct() {
        parent::__construct('fuel');
    }

    /**
     * Get fuel log row with descriptive metadata
     */
    public function findFuelWithDetails(int $id): ?array {
        global $wpdb;
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';
        
        $query = "SELECT f.*, 
                  v.vehicle_number, v.vehicle_type
                  FROM {$this->table_name} f
                  LEFT JOIN $table_vehicles v ON f.vehicle_id = v.id
                  WHERE f.id = %d AND f.deleted_at IS NULL";
                  
        $row = $wpdb->get_row($wpdb->prepare($query, $id), ARRAY_A);
        return $row ?: null;
    }

    /**
     * Get all fuel records with details
     */
    public function findAllWithDetails(array $params = []): array {
        global $wpdb;
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';

        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $allowed_sorts = ['id', 'fuel_date', 'fuel_quantity', 'fuel_cost'];
        $sort = isset($params['sort']) && in_array($params['sort'], $allowed_sorts) ? 'f.' . $params['sort'] : 'f.id';
        $order = isset($params['order']) && strtoupper($params['order']) === 'DESC' ? 'DESC' : 'ASC';

        $where = ["f.deleted_at IS NULL"];
        $args = [];

        // Apply filters
        if (!empty($params['vehicle_id'])) {
            $where[] = "f.vehicle_id = %d";
            $args[] = (int)$params['vehicle_id'];
        }
        if (!empty($params['trip_id'])) {
            $where[] = "f.trip_id = %d";
            $args[] = (int)$params['trip_id'];
        }

        // Search text matching
        if (!empty($params['search'])) {
            $search_val = '%' . $wpdb->esc_like($params['search']) . '%';
            $where[] = "(v.vehicle_number LIKE %s OR f.fuel_station LIKE %s)";
            $args[] = $search_val;
            $args[] = $search_val;
        }

        $where_clause = implode(" AND ", $where);

        // Get count
        $total_query = "SELECT COUNT(*) FROM {$this->table_name} f
                        LEFT JOIN $table_vehicles v ON f.vehicle_id = v.id
                        WHERE $where_clause";
        if (!empty($args)) {
            $total_count = (int)$wpdb->get_var($wpdb->prepare($total_query, $args));
        } else {
            $total_count = (int)$wpdb->get_var($total_query);
        }

        // Get rows
        $data_query = "SELECT f.*, 
                      v.vehicle_number, v.vehicle_type
                      FROM {$this->table_name} f
                      LEFT JOIN $table_vehicles v ON f.vehicle_id = v.id
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
