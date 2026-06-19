<?php
namespace WorkspaceErpApi\Repositories;

class AssetRepository extends BaseRepository {
    public function __construct() { parent::__construct('assets'); }
    public function existsAssetCode(string $code, ?int $exclude_id = null): bool { return $this->exists('asset_code', $code, $exclude_id); }
}
