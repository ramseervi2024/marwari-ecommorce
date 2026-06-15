<?php
namespace SchoolManagementApi\Repositories;

class DocumentRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('documents');
    }
}
