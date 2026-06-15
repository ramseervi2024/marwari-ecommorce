<?php
namespace SchoolManagementApi\Repositories;

class AttendanceRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('attendance');
    }
}
