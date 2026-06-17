<?php
namespace JewelleryManagementApi\Repositories;

class InventoryAuditRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_inventory_audit';
    }
}
