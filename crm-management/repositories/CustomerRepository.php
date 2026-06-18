<?php
namespace CrmManagementApi\Repositories;

class CustomerRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('customers', false);
    }

    public function findByUserId(int $user_id): ?array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE user_id = %d";
        $row = $wpdb->get_row($wpdb->prepare($query, $user_id), ARRAY_A);
        return $row ?: null;
    }

    public function findByCustomerCode(string $customer_code): ?array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE customer_code = %s";
        $row = $wpdb->get_row($wpdb->prepare($query, $customer_code), ARRAY_A);
        return $row ?: null;
    }
}
