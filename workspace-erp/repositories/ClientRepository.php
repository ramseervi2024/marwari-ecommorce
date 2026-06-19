<?php
namespace WorkspaceErpApi\Repositories;

class ClientRepository extends BaseRepository {
    public function __construct() { parent::__construct('clients'); }
    public function existsClientCode(string $code, ?int $exclude_id = null): bool { return $this->exists('client_code', $code, $exclude_id); }
}
