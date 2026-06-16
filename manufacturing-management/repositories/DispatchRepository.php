<?php
namespace ManufacturingManagementApi\Repositories;

class DispatchRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_dispatch';
    }
}
