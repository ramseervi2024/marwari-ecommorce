<?php
namespace AccountingManagementApi\Repositories;

class ItemRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('items', true);
    }

    public function existsItemCode(string $item_code, ?int $exclude_id = null): bool {
        return $this->exists('item_code', $item_code, $exclude_id);
    }
}
