<?php
namespace SchoolManagementApi\Repositories;

class NotificationRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('notifications');
    }
}
