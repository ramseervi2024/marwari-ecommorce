<?php
namespace SchoolManagementApi\Repositories;

class ParentRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('parents');
    }
}
