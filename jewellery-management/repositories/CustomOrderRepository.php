<?php
namespace JewelleryManagementApi\Repositories;

class CustomOrderRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_custom_orders';
    }
}
