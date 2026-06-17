<?php
namespace HrManagementApi\Repositories;

class DocumentRepository extends BaseRepository {
    
    public function __construct() {
        parent::__construct('documents', false);
    }
}
