<?php
namespace JewelleryManagementApi\Repositories;

class RepairRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_repairs';
    }
}
