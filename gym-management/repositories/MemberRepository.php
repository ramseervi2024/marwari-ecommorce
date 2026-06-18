<?php
namespace GymErpApi\Repositories;
class MemberRepository extends BaseRepository {
    public function __construct() { parent::__construct('members'); }
    public function generateMemberId(): string {
        global $wpdb;
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
        return 'GYM-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }
}
