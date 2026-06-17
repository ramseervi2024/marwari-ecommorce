<?php
namespace JewelleryManagementApi\Repositories;

class LoyaltyRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_loyalty';
    }
}
