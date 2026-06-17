<?php
namespace JewelleryManagementApi\Repositories;

class DiamondRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_diamonds';
    }
}
