<?php
namespace AccountingManagementApi\Repositories;

class VendorRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('vendors', true);
    }

    public function existsVendorCode(string $vendor_code, ?int $exclude_id = null): bool {
        return $this->exists('vendor_code', $vendor_code, $exclude_id);
    }
}
