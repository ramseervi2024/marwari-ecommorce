<?php
namespace JewelleryManagementApi\Repositories;

class KarigarRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_karigars';
    }
}
