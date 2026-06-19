<?php
namespace WorkspaceErpApi\Repositories;

class LeadRepository extends BaseRepository {
    public function __construct() { parent::__construct('leads'); }
    public function existsEmail(string $email, ?int $exclude_id = null): bool { return $this->exists('email', $email, $exclude_id); }
}
