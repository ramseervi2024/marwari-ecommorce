<?php
namespace AccountingManagementApi\Repositories;

class DocumentRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('documents', true);
    }
}
