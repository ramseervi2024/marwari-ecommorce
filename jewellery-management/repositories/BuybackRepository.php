<?php
namespace JewelleryManagementApi\Repositories;

class BuybackRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_buyback';
    }
}
