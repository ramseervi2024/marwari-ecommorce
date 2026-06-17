<?php
namespace RealEstateManagementApi\Repositories;

class PropertyRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('properties');
    }
}
