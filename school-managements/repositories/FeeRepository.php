<?php
namespace SchoolManagementApi\Repositories;

class FeeRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('fees');
    }
}
