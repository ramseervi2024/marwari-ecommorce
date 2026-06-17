<?php
namespace JewelleryManagementApi\Repositories;

class MetalStockRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_metal_stock';
    }
}
