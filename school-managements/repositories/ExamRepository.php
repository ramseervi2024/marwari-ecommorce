<?php
namespace SchoolManagementApi\Repositories;

class ExamRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('exams');
    }
}
