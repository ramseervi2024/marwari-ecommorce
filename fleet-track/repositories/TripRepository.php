<?php
namespace FleetTrackPro\Repositories;

class TripRepository extends BaseRepository {
    
    public function __construct() {
        parent::__construct('trips');
    }

    /**
     * Get a trip with details (joined vehicle, driver, route)
     */
    public function findTripWithDetails(int $id): ?array {
        global $wpdb;
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';
        $table_drivers = $wpdb->prefix . 'fleet_drivers';
        $table_routes = $wpdb->prefix . 'fleet_routes';
        
        $query = "SELECT t.*, 
                  v.vehicle_number, v.vehicle_type, v.vehicle_brand,
                  d.name as driver_name, d.phone as driver_phone,
                  r.route_name, r.source, r.destination, r.distance_km as route_distance_km
                  FROM {$this->table_name} t
                  LEFT JOIN $table_vehicles v ON t.vehicle_id = v.id
                  LEFT JOIN $table_drivers d ON t.driver_id = d.id
                  LEFT JOIN $table_routes r ON t.route_id = r.id
                  WHERE t.id = %d AND t.deleted_at IS NULL";
                  
        $row = $wpdb->get_row($wpdb->prepare($query, $id), ARRAY_A);
        return $row ?: null;
    }

    /**
     * Find all trips with detailed columns
     */
    public function findAllWithDetails(array $params = []): array {
        global $wpdb;
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';
        $table_drivers = $wpdb->prefix . 'fleet_drivers';
        $table_routes = $wpdb->prefix . 'fleet_routes';

        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $allowed_sorts = ['id', 'trip_date', 'revenue', 'distance_travelled', 'status'];
        $sort = isset($params['sort']) && in_array($params['sort'], $allowed_sorts) ? 't.' . $params['sort'] : 't.id';
        $order = isset($params['order']) && strtoupper($params['order']) === 'DESC' ? 'DESC' : 'ASC';

        $where = ["t.deleted_at IS NULL"];
        $args = [];

        // Apply filters
        if (!empty($params['vehicle_id'])) {
            $where[] = "t.vehicle_id = %d";
            $args[] = (int)$params['vehicle_id'];
        }
        if (!empty($params['driver_id'])) {
            $where[] = "t.driver_id = %d";
            $args[] = (int)$params['driver_id'];
        }
        if (!empty($params['route_id'])) {
            $where[] = "t.route_id = %d";
            $args[] = (int)$params['route_id'];
        }
        if (!empty($params['status'])) {
            $where[] = "t.status = %s";
            $args[] = sanitize_text_field($params['status']);
        }

        // Search text matching
        if (!empty($params['search'])) {
            $search_val = '%' . $wpdb->esc_like($params['search']) . '%';
            $where[] = "(v.vehicle_number LIKE %s OR d.name LIKE %s OR r.route_name LIKE %s)";
            $args[] = $search_val;
            $args[] = $search_val;
            $args[] = $search_val;
        }

        $where_clause = implode(" AND ", $where);

        // Get count
        $total_query = "SELECT COUNT(*) FROM {$this->table_name} t
                        LEFT JOIN $table_vehicles v ON t.vehicle_id = v.id
                        LEFT JOIN $table_drivers d ON t.driver_id = d.id
                        LEFT JOIN $table_routes r ON t.route_id = r.id
                        WHERE $where_clause";
        if (!empty($args)) {
            $total_count = (int)$wpdb->get_var($wpdb->prepare($total_query, $args));
        } else {
            $total_count = (int)$wpdb->get_var($total_query);
        }

        // Get rows
        $data_query = "SELECT t.*, 
                      v.vehicle_number, v.vehicle_type, v.vehicle_brand,
                      d.name as driver_name, d.phone as driver_phone,
                      r.route_name, r.source, r.destination, r.distance_km as route_distance_km
                      FROM {$this->table_name} t
                      LEFT JOIN $table_vehicles v ON t.vehicle_id = v.id
                      LEFT JOIN $table_drivers d ON t.driver_id = d.id
                      LEFT JOIN $table_routes r ON t.route_id = r.id
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
