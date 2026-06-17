<?php
namespace AccountingManagementApi\Repositories;

class JournalRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('journals', true);
    }

    public function existsJournalNumber(string $journal_number, ?int $exclude_id = null): bool {
        return $this->exists('journal_number', $journal_number, $exclude_id);
    }
}
