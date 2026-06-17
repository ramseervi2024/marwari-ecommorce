<?php
namespace RealEstateManagementApi\Repositories;

class RegistrationRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('registrations');
    }
}
