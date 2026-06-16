<?php
namespace ManufacturingManagementApi\Repositories;

abstract class BaseRepository {
    protected $wpdb;
    protected $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . $this->get_table_suffix();
    }

    abstract protected function get_table_suffix(): string;

    public function find(int $id) {
        $query = $this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id);
        return $this->wpdb->get_row($query, ARRAY_A);
    }

    public function all(int $limit = 100, int $offset = 0) {
        $query = "SELECT * FROM {$this->table_name} ORDER BY id DESC LIMIT $limit OFFSET $offset";
        return $this->wpdb->get_results($query, ARRAY_A);
    }

    public function create(array $data) {
        $result = $this->wpdb->insert($this->table_name, $data);
        if ($result === false) {
            return false;
        }
        return $this->wpdb->insert_id;
    }

    public function update(int $id, array $data) {
        $result = $this->wpdb->update($this->table_name, $data, ['id' => $id]);
        if ($result === false) {
            return false;
        }
        return true;
    }

    public function delete(int $id) {
        $result = $this->wpdb->delete($this->table_name, ['id' => $id]);
        return $result !== false;
    }

    public function count() {
        return intval($this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}"));
    }
}
