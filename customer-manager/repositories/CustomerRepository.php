<?php
namespace CustomerManager\Repositories;

use CustomerManager\Models\Customer;

class CustomerRepository {
    
    /**
     * Find a customer by ID (active only).
     */
    public function findById(int $id): ?Customer {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customers';
        
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d AND is_deleted = 0", $id),
            ARRAY_A
        );

        return $row ? new Customer($row) : null;
    }

    /**
     * Find customer by email (active only, case insensitive).
     */
    public function findByEmail(string $email, ?int $exclude_id = null): ?Customer {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customers';
        
        if ($exclude_id !== null) {
            $query = "SELECT * FROM $table_name WHERE LOWER(email) = LOWER(%s) AND id != %d AND is_deleted = 0";
            $row = $wpdb->get_row($wpdb->prepare($query, $email, $exclude_id), ARRAY_A);
        } else {
            $query = "SELECT * FROM $table_name WHERE LOWER(email) = LOWER(%s) AND is_deleted = 0";
            $row = $wpdb->get_row($wpdb->prepare($query, $email), ARRAY_A);
        }

        return $row ? new Customer($row) : null;
    }

    /**
     * Find customer by phone (active only).
     */
    public function findByPhone(string $phone, ?int $exclude_id = null): ?Customer {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customers';
        
        if ($exclude_id !== null) {
            $query = "SELECT * FROM $table_name WHERE phone = %s AND id != %d AND is_deleted = 0";
            $row = $wpdb->get_row($wpdb->prepare($query, $phone, $exclude_id), ARRAY_A);
        } else {
            $query = "SELECT * FROM $table_name WHERE phone = %s AND is_deleted = 0";
            $row = $wpdb->get_row($wpdb->prepare($query, $phone), ARRAY_A);
        }

        return $row ? new Customer($row) : null;
    }

    /**
     * Get paginated, searched, and sorted list of customers.
     */
    public function findAll(array $params = []): array {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customers';

        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $allowed_sorts = ['id', 'first_name', 'last_name', 'email', 'phone', 'created_at', 'status'];
        $sort = isset($params['sort']) && in_array($params['sort'], $allowed_sorts) ? $params['sort'] : 'id';
        $order = isset($params['order']) && strtoupper($params['order']) === 'DESC' ? 'DESC' : 'ASC';

        $where = ["is_deleted = 0"];
        $args = [];

        // Filter by status
        if (!empty($params['status']) && in_array(strtoupper($params['status']), ['ACTIVE', 'INACTIVE'])) {
            $where[] = "status = %s";
            $args[] = strtoupper($params['status']);
        }

        // Search wildcard matching
        if (!empty($params['search'])) {
            $search_val = '%' . $wpdb->esc_like($params['search']) . '%';
            $where[] = "(first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR phone LIKE %s OR city LIKE %s OR state LIKE %s)";
            $args[] = $search_val;
            $args[] = $search_val;
            $args[] = $search_val;
            $args[] = $search_val;
            $args[] = $search_val;
            $args[] = $search_val;
        }

        $where_clause = implode(" AND ", $where);

        // Fetch total count matching parameters
        $total_query = "SELECT COUNT(*) FROM $table_name WHERE $where_clause";
        if (!empty($args)) {
            $total_count = (int)$wpdb->get_var($wpdb->prepare($total_query, $args));
        } else {
            $total_count = (int)$wpdb->get_var($total_query);
        }

        // Fetch data
        $data_query = "SELECT * FROM $table_name WHERE $where_clause ORDER BY $sort $order LIMIT %d OFFSET %d";
        $data_args = array_merge($args, [$limit, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($data_query, $data_args), ARRAY_A);

        $customers = [];
        foreach ($rows as $row) {
            $customers[] = new Customer($row);
        }

        return [
            'total' => $total_count,
            'data' => $customers
        ];
    }

    /**
     * Create a new customer record.
     */
    public function create(Customer $customer): ?int {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customers';

        $result = $wpdb->insert(
            $table_name,
            [
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'city' => $customer->city,
                'state' => $customer->state,
                'country' => $customer->country,
                'postal_code' => $customer->postal_code,
                'status' => $customer->status,
                'is_deleted' => 0,
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d']
        );

        return $result !== false ? (int)$wpdb->insert_id : null;
    }

    /**
     * Update an existing customer record.
     */
    public function update(int $id, array $data): bool {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customers';

        $allowed_fields = [
            'first_name', 'last_name', 'email', 'phone', 
            'address', 'city', 'state', 'country', 'postal_code', 'status'
        ];

        $update_data = [];
        $format = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed_fields)) {
                $update_data[$key] = $value;
                $format[] = '%s';
            }
        }

        if (empty($update_data)) {
            return false;
        }

        $result = $wpdb->update(
            $table_name,
            $update_data,
            ['id' => $id],
            $format,
            ['%d']
        );

        return $result !== false;
    }

    /**
     * Soft delete a customer (mark is_deleted = 1).
     */
    public function delete(int $id): bool {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customers';

        $result = $wpdb->update(
            $table_name,
            ['is_deleted' => 1],
            ['id' => $id],
            ['%d'],
            ['%d']
        );

        return $result !== false;
    }

    /**
     * Fetch customer metrics/statistics.
     */
    public function getStats(): array {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customers';

        $total = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_deleted = 0");
        $active = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'ACTIVE' AND is_deleted = 0");
        $inactive = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'INACTIVE' AND is_deleted = 0");

        return [
            'totalCustomers' => $total,
            'activeCustomers' => $active,
            'inactiveCustomers' => $inactive
        ];
    }
}
