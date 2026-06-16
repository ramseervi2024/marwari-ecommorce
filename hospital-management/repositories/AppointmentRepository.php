<?php
namespace HospitalManagementApi\Repositories;

class AppointmentRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('appointments');
    }
}
